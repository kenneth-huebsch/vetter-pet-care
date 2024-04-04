<?php
namespace Craft;

class WorkflowController extends BaseController
{
    // Public Methods
    // =========================================================================

    //
    // Control Panel
    //

    public function actionDrafts()
    {
        $records = EntryDraftRecord::model()->findAll();
        $drafts = EntryDraftModel::populateModels($records);

        $this->renderTemplate('workflow/drafts', array(
            'entries' => $drafts,
        ));
    }

    public function actionSettings()
    {
        $settings = craft()->workflow->getSettings();

        $this->renderTemplate('workflow/settings', array(
            'settings' => $settings,
        ));
    }


    //
    // Front-End
    //
    public function actionSendForSubmission()
    {
        $user = craft()->userSession->getUser();

        $model = new Workflow_SubmissionModel();
        $model->ownerId = craft()->request->getParam('entryId');
        $model->draftId = craft()->request->getParam('draftId');
        $model->editorId = $user->id;
        $model->status = Workflow_SubmissionModel::PENDING;
        $model->dateApproved = null;

        if (craft()->workflow_submissions->save($model)) {
            craft()->userSession->setNotice(Craft::t('Entry submitted for approval.'));
        } else {
            craft()->userSession->setError(Craft::t('Could not submit for approval.'));
        }

        // Redirect page to the entry as its not a form submission
        craft()->request->redirect(craft()->request->urlReferrer);
    }

    public function actionRevokeSubmission()
    {
        $submissionId = craft()->request->getParam('submissionId');

        $model = craft()->workflow_submissions->getById($submissionId);
        $model->status = Workflow_SubmissionModel::REVOKED;
        $model->dateRevoked = new DateTime;

        if (craft()->workflow_submissions->revokeSubmission($model)) {
            craft()->userSession->setNotice(Craft::t('Submission revoked.'));
        } else {
            craft()->userSession->setError(Craft::t('Could not revoke submission.'));
        }

        // Redirect page to the entry as its not a form submission
        craft()->request->redirect(craft()->request->urlReferrer);
    }

    public function actionApproveSubmission()
    {
        $user = craft()->userSession->getUser();

        $draftId = craft()->request->getParam('draftId');
        $submissionId = craft()->request->getParam('submissionId');
        $notes = craft()->request->getParam('notes');

        $model = craft()->workflow_submissions->getById($submissionId);
        $model->status = Workflow_SubmissionModel::APPROVED;
        $model->publisherId = $user->id;
        $model->dateApproved = new DateTime;
        $model->notes = $notes;

        // Check if we're approving a draft - we publish it too.
        if ($draftId) {
            $draft = craft()->entryRevisions->getDraftById($draftId);
        } else {
            $draft = null;
        }

        if (craft()->workflow_submissions->approveSubmission($model, $draft)) {
            craft()->userSession->setNotice(Craft::t('Entry approved and published.'));
        } else {
            craft()->userSession->setError(Craft::t('Could not approve and publish.'));
        }

        // Redirect page to the entry as its not a form submission - check for draft
        if ($draft) {
            // If we've published a draft the url has changed
            craft()->request->redirect($draft->cpEditUrl);
        } else {
            craft()->request->redirect(craft()->request->urlReferrer);
        }
    }

    public function actionRejectSubmission()
    {
        $user = craft()->userSession->getUser();

        $draftId = craft()->request->getParam('draftId');
        $submissionId = craft()->request->getParam('submissionId');
        $notes = craft()->request->getParam('notes');

        $model = craft()->workflow_submissions->getById($submissionId);
        $model->status = Workflow_SubmissionModel::REJECTED;
        $model->publisherId = $user->id;
        $model->dateRejected = new DateTime;
        $model->notes = $notes;

        if (craft()->workflow_submissions->rejectSubmission($model)) {
            craft()->userSession->setNotice(Craft::t('Submission rejected.'));
        } else {
            craft()->userSession->setError(Craft::t('Could not reject submission.'));
        }

        // Redirect page to the entry as its not a form submission
        craft()->request->redirect(craft()->request->urlReferrer);
    }





    // Private Methods
    // =========================================================================

    /*private function _response($model = null)
    {
        // Handle Ajax response
        if (craft()->request->isAjaxRequest()) {
            $this->returnJson($model);
        } else {
            $this->_redirect($model);
        }
    }

    private function _redirect($model)
    {
        $url = craft()->request->getPost('redirect');

        if ($url === null) {
            $url = craft()->request->getParam('return');

            if ($url === null) {
                $url = craft()->request->getUrlReferrer();

                if ($url === null) {
                    $url = '/';
                }
            }
        }

        craft()->request->redirect($url);
    }*/
}
