/**
 * Folder tree for WP Media Folder
 */
var wpmfFoldersTreeCategoriesModule;
(function ($) {
    wpmfFoldersTreeCategoriesModule = {
        categories: [], // categories
        folders_states: [], // Contains open or closed status of folders

        /**
         * Retrieve the Jquery tree view element
         * of the current frame
         * @return jQuery
         */
        getTreeElement: function () {
            return $('#wpmf_foldertree_categories').find('.wpmf-folder-tree');
        },

        /**
         * Initialize module related things
         */
        initModule: function () {
            // Import categories from wpmf main module
            wpmfFoldersTreeCategoriesModule.importCategories();

            // Add the tree view to the main content
            $('<div class="wpmf-folder-tree wpmf-no-margin wpmf-no-padding"></div>').appendTo($('#wpmf_foldertree_categories'));

            // Render the tree view
            wpmfFoldersTreeCategoriesModule.loadTreeView();
        },

        getchecked: function (folder_id, button) {
            $('#wpmf_foldertree_categories .media_checkbox').not(button).prop('checked', false);
            if ($(button).is(':checked')) {
                wpmfFoldersTreeCategoriesModule.renderBreadCrumb(folder_id);
            } else {
                wpmfFoldersTreeCategoriesModule.renderBreadCrumb(0);
            }
        },

        /**
         * Import categories from wpmf main module
         */
        importCategories: function () {
            var folders_ordered = [];

            // Add each category
            $(wpmf.vars.wpmf_categories_order).each(function () {
                folders_ordered.push(wpmf.vars.wpmf_categories[this]);
            });

            // Reorder array based on children
            var folders_ordered_deep = [];
            var processed_ids = [];
            var loadChildren = function (id) {
                if (processed_ids.indexOf(id) < 0) {
                    processed_ids.push(id);
                    for (var ij = 0; ij < folders_ordered.length; ij++) {
                        if (folders_ordered[ij].parent_id === id) {
                            folders_ordered_deep.push(folders_ordered[ij]);
                            loadChildren(folders_ordered[ij].id);
                        }
                    }
                }
            };
            loadChildren(parseInt(wpmf.vars.term_root_id));

            // Finally save it to the global var
            wpmfFoldersTreeCategoriesModule.categories = folders_ordered_deep;

        },

        /**
         * Render tree view inside content
         */
        loadTreeView: function () {
            wpmfFoldersTreeCategoriesModule.getTreeElement().html(wpmfFoldersTreeCategoriesModule.getRendering());
        },

        /**
         * Get the html resulting tree view
         * @return {string}
         */
        getRendering: function () {
            var ij = 0;
            var content = ''; // Final tree view content
            /**
             * Recursively print list of folders
             * @return {boolean}
             */
            var generateList = function generateList() {
                content += '<ul>';
    
                while (ij < wpmfFoldersTreeCategoriesModule.categories.length) {
                    var className = 'closed';
                    // Open li tag
                    content += '<li class="' + className + '" data-id="' + wpmfFoldersTreeCategoriesModule.categories[ij].id + '" >';

                    var a_tag = '<a data-id="' + wpmfFoldersTreeCategoriesModule.categories[ij].id + '">';

                    // get color folder
                    var bgcolor = '';
                    if (typeof wpmf.vars.colors !== 'undefined' && typeof wpmf.vars.colors[wpmfFoldersTreeCategoriesModule.categories[ij].id] !== 'undefined' && wpmfFoldersModule.folder_design === 'material_design') {
                        bgcolor = 'color: ' + wpmf.vars.colors[wpmfFoldersTreeCategoriesModule.categories[ij].id];
                    } else {
                        bgcolor = 'color: #8f8f8f';
                    }

                    if (wpmfFoldersTreeCategoriesModule.categories[ij + 1] && wpmfFoldersTreeCategoriesModule.categories[ij + 1].depth > wpmfFoldersTreeCategoriesModule.categories[ij].depth) { // The next element is a sub folder
                        content += '<a onclick="wpmfFoldersTreeCategoriesModule.toggle(' + wpmfFoldersTreeCategoriesModule.categories[ij].id + ')"><i class="material-icons wpmf-arrow">keyboard_arrow_down</i></a>';

                        content += a_tag;

                        // Add folder icon
                        content += '<i class="material-icons" style="' + bgcolor + '">folder</i>';
                    } else {
                        content += a_tag;

                        // Add folder icon
                        content += '<i class="material-icons wpmf-no-arrow" style="' + bgcolor + '">folder</i>';
                    }

                    content += '<input type="checkbox" class="media_checkbox" onclick="wpmfFoldersTreeCategoriesModule.getchecked(' + wpmfFoldersTreeCategoriesModule.categories[ij].id + ',  this)" data-id="' + wpmfFoldersTreeCategoriesModule.categories[ij].id + '" />';

                    // Add current category name
                    if (wpmfFoldersTreeCategoriesModule.categories[ij].id === 0) {
                        // If this is the root folder then rename it
                        content += '<span onclick="wpmfFoldersTreeCategoriesModule.changeFolder(0)">' + wpmf.l18n.media_folder + '</span>';
                    } else {
                        content += '<span onclick="wpmfFoldersTreeCategoriesModule.changeFolder(' + wpmfFoldersTreeCategoriesModule.categories[ij].id + ')">' + wpmfFoldersTreeCategoriesModule.categories[ij].label + '</span>';
                    }

                    content += '</a>';
                    // This is the end of the array
                    if (wpmfFoldersTreeCategoriesModule.categories[ij + 1] === undefined) {
                        // var's close all opened tags
                        for (var ik = wpmfFoldersTreeCategoriesModule.categories[ij].depth; ik >= 0; ik--) {
                            content += '</li>';
                            content += '</ul>';
                        }

                        // We are at the end don't continue to process array
                        return false;
                    }


                    if (wpmfFoldersTreeCategoriesModule.categories[ij + 1].depth > wpmfFoldersTreeCategoriesModule.categories[ij].depth) { // The next element is a sub folder
                        // Recursively list it
                        ij++;
                        if (generateList() === false) {
                            // We have reached the end, var's recursively end
                            return false;
                        }
                    } else if (wpmfFoldersTreeCategoriesModule.categories[ij + 1].depth < wpmfFoldersTreeCategoriesModule.categories[ij].depth) { // The next element don't have the same parent
                        // var's close opened tags
                        for (var ik1 = wpmfFoldersTreeCategoriesModule.categories[ij].depth; ik1 > wpmfFoldersTreeCategoriesModule.categories[ij + 1].depth; ik1--) {
                            content += '</li>';
                            content += '</ul>';
                        }

                        // We're not at the end of the array var's continue processing it
                        return true;
                    }

                    // Close the current element
                    content += '</li>';
                    ij++;
                }
            };

            // Start generation
            generateList();
            return content;
        },

        /**
         * Change the selected folder in tree view
         * @param folder_id
         */
        changeFolder: function (folder_id) {
            // Remove previous selection
            wpmfFoldersTreeCategoriesModule.getTreeElement().find('li').removeClass('selected');

            // Select the folder
            wpmfFoldersTreeCategoriesModule.getTreeElement().find('li[data-id="' + folder_id + '"]').addClass('selected').// Open parent folders
            parents('.wpmf-folder-tree li.closed').removeClass('closed');
        },

        /**
         * Change the selected folder in tree view
         * @param folder_id
         */
        renderBreadCrumb: function (folder_id) {
            if (parseInt(folder_id) === 0) {
                $('.dir_name_categories').val('/').data('id_category' , 0);
            } else {
                var category = wpmf.vars.wpmf_categories[folder_id];
                var breadcrumb_content = '';

                // Ascend until there is no more parent
                while (parseInt(category.parent_id) !== parseInt(wpmf.vars.parent)) {
                    // Generate breadcrumb element
                    breadcrumb_content = '/' + wpmf.vars.wpmf_categories[category.id].label + breadcrumb_content;

                    // Get the parent
                    category = wpmf.vars.wpmf_categories[wpmf.vars.wpmf_categories[category.id].parent_id];
                }

                if (parseInt(category.id) !== 0) {
                    breadcrumb_content = wpmf.vars.wpmf_categories[category.id].label + breadcrumb_content;
                }

                breadcrumb_content = '/' + breadcrumb_content + '/';
                $('.dir_name_categories').val(breadcrumb_content).data('id_category' , folder_id);
            }

        },

        /**
         * Toggle the open / closed state of a folder
         * @param folder_id
         */
        toggle: function (folder_id) {
            // Check is folder has closed class
            if (wpmfFoldersTreeCategoriesModule.getTreeElement().find('li[data-id="' + folder_id + '"]').hasClass('closed')) {
                // Open the folder
                wpmfFoldersTreeCategoriesModule.openFolder(folder_id);
            } else {
                // Close the folder
                wpmfFoldersTreeCategoriesModule.closeFolder(folder_id);
                // close all sub folder
                $('li[data-id="' + folder_id + '"]').find('li').addClass('closed');
            }
        },


        /**
         * Open a folder to show children
         */
        openFolder: function (folder_id) {
            wpmfFoldersTreeCategoriesModule.getTreeElement().find('li[data-id="' + folder_id + '"]').removeClass('closed');
            wpmfFoldersTreeCategoriesModule.folders_states[folder_id] = 'open';
        },

        /**
         * Close a folder and hide children
         */
        closeFolder: function (folder_id) {
            wpmfFoldersTreeCategoriesModule.getTreeElement().find('li[data-id="' + folder_id + '"]').addClass('closed');
            wpmfFoldersTreeCategoriesModule.folders_states[folder_id] = 'close';
        }
    };

    // var's initialize WPMF folder tree features
    $(document).ready(function () {
        wpmfFoldersTreeCategoriesModule.initModule();
    });
})(jQuery);