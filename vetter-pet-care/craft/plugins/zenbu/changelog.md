# Changelog

## 1.3.2

- **Fix**: Fixed a potential template error ("_Key id for array with keys..._") that would display and then get cached in the temporary cache used for returning to a Zenbu page.
- **Fix**: Filtering using more than one _Dropdown_, _Radio Button_, or _Position Select_ filter would only use the last filter. Filters for these fields now stack as they should, eg. **Before**: "Not A, Not B" would display "C" and "A", **Now**: "Not A, Not B" would display only "C".

## 1.3.1

- **Fix**: Author dropdown can now contain more than 100 users.
- **Fix**: Updated Vue.js library which should fix an issue where going from "All sections" to another section threw a js console error, as well as didn't update results.
- **Fix**: Fixed a display issue in the datepicker when filtering by post date, etc.
- **Fix**: Fixed a potential issue where singles would display no entries when there should obviously be one displayed.
- **Improved**: There are cases where Zenbu fires a few too many ajax search queries at once (common situation: search query is triggered twice when one would be enough). Zenbu should be firing search queries less freqently now, hopefully making Zenbu feel a bit _lighter_.
- **Improved**: When an input field is focused, pressing _any_ key (and I mean _any_, even just shift or control/command) would fire a (unnecessary) search query. This should now happen only when the field changes in any significant manner.
- **Improved**: Optimized how asset files are organized and packaged.

## 1.3.0

- **New**: Support for **Create Date** and **Update Date**
- **Improved**: Zenbu was partially rewritten so that you can enjoy an overall _much_ better addon performance.
- **Improved**: User-defined/Custom display templates are now also used in native Craft entry listings ("Use in Native Listing" still needs to be enabled first for your column).
- **Improved**: The browser’s address bar updates when a different section or entry type is selected in the section and entry type dropdowns.
- **Fix**: Fixed an issue where saving display settings to other users would not update those user's settings and data because that data was still being cached.
- **Fix**: Fixed an issue where ordering by author would display an error instead of what you probably hoped to see: entry results.
- **Fix**: Fixed an issue where ordering and sorting was not being remembered when coming back to Zenbu after saving an entry.
- **Fix**: Fixed an issue where saved searches would not load ordering and sorting values correctly.
- **Fix**: Fixed an issue with clearing cache for entry display when saving Display Settings. Only the currently used locale was being cleared, now they all do.


## 1.2.2

- **Fix**: Fixed an issue where ordering and sorting was not being remembered when coming back to Zenbu after saving an entry.
- **Fix**: Fixed an issue where saved searches would not load ordering and sorting values correctly.
- **Fix**: Fixed an issue with clearing cache for entry display when saving Display Settings. Only the currently used locale was being cleared, now they all do.
- **Improved**: User-defined/Custom display templates are now also used in native Craft entry listings ("Use in Native Listing" still needs to be enabled first for your column).

## 1.2.1.1

- **Fix**: Fixed an issue where an error about `customDisplayStringTemplate` would display when trying to save Display Settings. If the issue persists after this update, one solution is to uninstall and reinstall Zenbu (you will lose your previous configuration, but this can be a handy fix for fresh installs).

## 1.2.1

- **Fix**: Some sections would not display entries if the section only had one or more locales that happened to be different from the Craft installation's base locale. This shouldn't happen anymore, i.e. entries should now display.
- **Fix**: Fixed an issue where saving new entries followed by redirection to Zenbu would work fine but potentially not load entry results.
- **Fix**: Fixed an issue with internal file loading in some environments.
- **Improved**: Better caching of internal Zenbu data fetching.

## 1.2.0

- **New**: **User-defined/Custom display templates**. This is big: allows you use your own Craft/Twig code to control the display of data in the entry listing. Don't like the stock display options? Fieldtype not supported? Need to tweak the display of your entry data? Now you can set up entry data display to your needs. [Read about and see an example of this feature](https://zenbustudio.com/blog/coming-soon-to-zenbu-for-craft-user-defined-custom-display).
- **New**: Ordering by textual custom fields is now possible.
- **New**: Ordering by **Structure Order** is now possible for _Structure_ type sections.
- **New**: Visual display of _Structure_ hiearchy (when ordering by **Structure Order** is selected)
- **Fix**: Fixed an issue where searching on categories, tags, or assets would return only up to 100 results.
- **Fix**: Fixed an issue where searching on categories, tags, or assets would return only open entries.
- **Fix**: Fixed a potential PHP error that may arise on Craft 2.6.2951 and under while retrieving the Craft build number.

## 1.1.0

- **New**: **Date** field searches: _before X_, _after X_, _on X_, _in the next X days_, _in the last X days_, _between [2 dates]_
- **New**: Added the option to display related entry status in related **Entries** fields.
- **New**: Added the option to display an entry author's status, thumbnail, and name as a link to his/her acocunt or plain text.
- **Fix**: Related **Entries** fields now display entries from all statuses, not only live.
- **Fix**: Fixed fatal errors after upgrading Craft to 2.6.2951 or higher.

## 1.0.2

- **Fix**: Fix to Category dropdown display.

## 1.0.1

- **Fix**: Fixed an issue where Category data would potentially not load correctly.
- **Fix**: Fixed an issue where the Display Settings field listing was not immediately refreshed after adding/removing/moving field(s) in a section.

## 1.0.0

- **New**: **Category** dropdown filter
- **New**: **Radio Buttons** dropdown filter
- **New**: **Dropdown** fields are now searchable using a select/dropdown field instead of an input field.
- **New**: **Lightswitch** filter using a dropdown and Lightswitch data display
- **New**: **Position Select** search dropdown and display options
- **New**: **Locale** searching of entries
- **New**: **Post/Expiry date** searches: _in the next X days_, _in the last X days_, _between [2 dates]_
- **New**: Zenbu's _Display Settings_ can be applied to the native Craft entry listing. This is handy for users who want the field display flexibility of Zenbu, but still want to stick to the native Craft entry listing.
- **New**: When activated in Zenbu, fields such as textareas, Rich Text, status, etc, are made available in the native Craft entry listing.
- **Improved**: Javascript handling of Zenbu form elements now powered by vue.js instead of painfully done through jQuery. This results in a more robust, and especially consistent, interface behaviour.
- **Improved**: Entry listing fades out slightly when it's a search is in progress to indicate a search is underway.
- **Fix**: Returning to Zenbu after saving an entry now respects display settings.
- **Fix**: Returning to Zenbu after saving an entry now loads the correct section/entry type combination.
- **Fix**: Fixed potential issues with "does not contain" searches on Category fields.

## 0.11.4

- **Fix**: Fixed an issue where the sidebar open/closed state would not be saved on installations using csrf tokens.

## 0.11.3

- **Improved**: URLs in Zenbu subnavigation items update based on selected section.
- **Improved**: Total results display even when there is no pagination.
- **Improved**: Zenbu now remembers the open/closed state of its sidebar.
- **Improved**: Sidebar _slides_ open/closed. You can put your socks back on.
- **Improved**: The user group checkboxes in _Display Settings_, to apply display settings to selected user groups, now has a _Select All_ checkbox to check/uncheck all user groups at once.
- **Fix**: Fixed an issue where creating a Single would redirect to a not-yet-valid Zenbu URL, dropping you off at an error page.

## 0.11.2

- **Improved**: Added plugin icons.
- **Improved**: Zenbu's top tabs in Craft 2.5+ are replaced with subnavigation items.
- **Improved**: Added plugin description and documentation URLs.
- **Improved**: A number of under-the-hood improvements.
- **Improved**: Zenbu now checks for updates.

## 0.11.1

- **Fix**: Fixed an issue with pagination not refreshing result display.
- **Fix**: Fixed a class loading error that can occur on case-sensitive servers.

- **New**: Craft 2.6 support, Craft 2.5 is the new minimum requirement (2.1+ should still work fine)
- **Fix**: Fixed visual glitch where the sidebar open/close icon was not positioned correctly in Craft 2.5 and up.
- **Fix**: Fixed rendering issues in Craft 2.6.
- **Fix**: searching by URI now works correctly

## 0.10.0

- **New**: Plugin setting to hide the Entries tab in the Control Panel.
- **New**: Entry section name is now displayed in its own column.
- **New**: Empty/Not Empty searches. Covers simple fields such as input and textarea fields, not more complex fields such as Matrix, Tags, Categories, Assets, Entries, etc.
- **Improved**: Tweaks to how searches in Zenbu are prepared and queried, leading to better performance (~25-75% improvement, depending on the data being retrieved).
- **Improved**: "Zenbu" tab label renaming uses the available modifyCpNav hook instead of javascript, when available. In other words, the "text change flashy thing" doesn't happen anymore.
- **Improved**: Removed "Std" label in Display Settings because it could mean so many things (hint: it meant "Standard") and replaced it with a classy dash.
- **Improved**: Reduced saved search caching, because it was way too aggressive.
- **Fix**: Fixed an issue where newly created fields would not show up in Zenbu right away, again because of aggressive caching.
- **Fix**: Fixed an issue where temporarily cached filters (eg. when reloading/returning to Zenbu) would not select the correct second filter dropdown value.

## 0.9.0

- **New**: Added "New Entry" button (with a dropdown for each section) when "All Sections" is selected. It's the little things.
- **Improved**: Changed _word_ limit to _character_ limit. More consistent lengths, also works with languages that don't use spaces (eg. Japanese).
- **Improved**: First filter is focused on page load, saving you and your wrist from reaching the mouse or _infinitabbing_ to the field.
- **Improved**: Returning to Zenbu when it remembers your previous search doesn't do the ol' flash-of-entries-then-replaced-with-fitlered-entries behaviour anymore.
- **Improved**: Removed "Order by" text in filters, since browsers couldn't agree on how to display the text.
- **Improved**: Some internal refactoring.
- **Fixed**: Fixed an issue where the "New Entry" button would assume the CP URL always used "admin". Never assume too much, yet we all fall for it from time to time.
- **Fixed**: Fixed an error that would show up when visiting Display Settings, changing the section dropdown to something other than "All Sections", then changing that _back_ to "All Sections", and finally clicking the "Entry Listing" tab. This sadly wasn't a secret combo to unlock God Mode.
- **Fixed**: Fixed an issue in which some dropdowns in the filters would have "empty" selections when other dropdowns are changed, triggering errors.

## 0.8.1

- **Fix**: Fixed an issue with Zenbu showing only 100 total entries, even when showing 200, 500 results was chosen
- **Fix**: Fixed a potential issue which would throw the YDSOD (Yellow Debug Screen Of Death) if a section and entry type ID combination didn't exist (in this universe).

## 0.8

- **New**: Zenbu (foam) memory: When you leave Zenbu's main page, eg. when loading an entry form, Zenbu will remember your last search when you return to Zenbu and have all filters in place. Like foam memory (cache), Zenbu forgets the last search after 2 hours of Zenbu search inactivity.
- **Improved**: Added 100, 200, 500 results per page options, and removed 1, 2 results per page. Who needs one result per page besides the plugin dev?
- **Improved**: Added row highlighting on hover. No need to pull out the ol' ruler to know which row your mouse is on.
- **Improved**: Cleaned up a few style class names. _A few_.
- **Fix**: Fixed a potential issue with unknown/3rd-party fieldtypes in Matrix fields throwing an error, making Zenbu less enjoyable.

## 0.7

- Added related Entries listing display in Matrix fields.
- Title is the first item in dropdowns, as it is more useful than ID.
- "Show" links for Matrix only show when there is Matrix data in the field. Otherwise the cell is now empty.
- Matrix fields are set to show a "Show" link by default (eg. initial install). Less intimidating display on first install.
- Post date format setting is empty by default, meaning the full timestamp now displays by default.
- Fixed an issue where editing an entry and selecting "Save and continue" would redirect to Zenbu when it shouldn't.

## 0.6

- Added "New Entry" button, to create new entries from Zenbu.
- Added "Redirect to Zenbu on entry save" option in Zenbu's plugin settings. Accesible from CP => Plugins => Zenbu
- The "Can copy Display Settings to other users" permission only displays for user groups, not individual user permissions.
- When a multi-filter saved search is selected while there is initially only one filter row, the minus buttons now display.
- Entries without a post date do not display the current date by default anymore.
- Show X results and Order buttons are not vertically aligned (and looking like crap) in IE anymore.
- More robust multi-filter searching.

## 0.5

- Added User permissions to allow a User or Group to copy display settings to other users. Found in Craft's Users section.
- Users that are not allowed to delete entries should not get the option to do so.
- Entries that the logged in user is not allowed to view or publish will not display in the entry list.
- Fixed and issue where author filtering was not working properly.
- Fixed an issue with pagination when csrf protection is enabled.
- Entries that don't have a URI do not display a link and home icon anymore.
- Changing the first dropdown of a search filter to Post Date now clears the 3rd field to avoid errors.
- Fixed an issue where switching the section dropdown from a section to All Sections cleared the first dropdown in filters.
- Saved Searches now save the number of entries to show on one page ("Show X results" field)
- Slightly better modal window handling for Asset images, especially for large images.
- Added js-based csrf definition where it was missing.

## 0.4.1

- Scrollbars do not appear in the sidebar anymore (eg. Firefox).
- Fixed an issue where if the URI was the homepage, the URI link would point to zenbu in the CP.
- The (-) button does not display anymore if there is only one search filter.
- Fixed an issue where newly added saved searches would then not be ordered correctly.
- Fixed an issue where iframe in modal windows (eg. for images) would not fill the modal window.
- Fixed an issue where scrollbars would not appear in modal windows containing Matrix data longer than the window.
- Added proper asset searching based on filename (can be slow, but the other option was it not working).
- Fixed an issue where tab Urls would not update when a saved search was selected.
- Fixed an issue where numeric fields in Display Settings could accept negative values.
- Fixed an issue where empty Singles/Channels/Structures would still display an optgroup of that type in dropdowns.
- Fixed an issue in the Saved Search Manager where the "Other Actions" button would display even before selecting any saved searches.

## 0.4

- Fixes to Display Setting saving for users
- Renamed some controller files/classes
- Added general plugin settings section. Accesible from CP => Plugins => Zenbu
- Users can set the top navigation and plugin header text to their own custom text. Can be set in CP => Plugins => Zenbu

## 0.3

- Private Beta