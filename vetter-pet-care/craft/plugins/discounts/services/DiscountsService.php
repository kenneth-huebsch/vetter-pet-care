<?php
/**
 * Displaced Appointments plugin for Craft CMS
 *
 * DisplacedAppointments_AppointmentsService Service
 *
 * @author    Matthew Cieslak
 * @copyright Copyright (c) 2017 Matthew Cieslak
 * @link      https://github.com/mattman93
 * @package   DisplacedAppointments
 * @since     1.0.0
 */

namespace Craft;

class DiscountsService extends BaseApplicationComponent
{
    public function RedeemCoupon()
    {
        $couponCodeMatrix = craft()->fields->getFieldByHandle("coupons_used");
        $userid = craft()->request->getPost('userid');
        $discount_code = craft()->request->getPost('discount_code');
        $userModel = craft()->users->getUserById($userid);
        $criteria = craft()->elements->getCriteria(ElementType::MatrixBlock);
        $criteria->fieldId = $couponCodeMatrix->id;
        $criteria->ownerId = $userModel->id; // This is your Global Set id
        $matrixBlocks = $criteria->find();

        foreach ($matrixBlocks as $block) {
            if ($block->coupon_code == $discount_code && $block->used_status == "unused") {
                $block->getContent()->setAttributes(array(
                    'used_status' => "used"
                ));
                $success = craft()->matrix->saveBlock($block);
            }
        }
        $this->returnJson(array('results' => "done"));
    }
}