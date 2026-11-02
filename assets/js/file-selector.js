$(document).ready(function () {
    "use strict";
    /* File selector */
    var $button = $('.file-manager-selector-button');

    var loadItem = function ($sId, filesid) {
        var $imageHolder = $sId.next('.filemanager').find('.file-manager-selector-file');

        if (filesid) {
            $('<div>').load($imageHolder.data('link'), {'filesStructureFilesId': filesid}, function () {
                var $selectedImage = $($(this).html());

                $selectedImage.find('.rename').hide();
                $selectedImage.find('.info').hide();
                $selectedImage.find('.download').hide();
                var removeLabel = $imageHolder.closest('.filemanager').data('remove-label');
                $selectedImage.find('.delete').attr({href: '#', title: removeLabel, 'aria-label': removeLabel}).removeAttr('data-confirm').click(function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $sId.val('').trigger('change');
                    $selectedImage.remove();
                });


                $imageHolder.html($selectedImage);
            });
        }
    };

    var loader = function (obj, params) {
        var $target = $(obj.attr('data-bs-target'));
        $('<div>').load(obj.data('link'), params, function () {
            var $manager = $($(this).html());
            $manager.find(".fileitem").click(function (e) {
                e.preventDefault();
                e.stopPropagation();
                var filesid = $(this).data('filesid');
                var $sId = obj.closest('.file-manager-selector').find('.file-manager-selector-id');
                $sId.val(filesid).trigger('change');
                loadItem($sId, filesid);

                $target.modal('hide');
            });

            $manager.find(".back").click(function (e) {
                e.preventDefault();
                var structureid = $(this).data('backid');
                if (structureid) {
                    params['structureId'] = structureid;
                }
                else {
                    delete params['structureId'];
                }
                loader(obj, params);
            });

            $manager.find(".folder").click(function (e) {
                e.preventDefault();
                var structureid = $(this).data('structureid');
                if (structureid) {
                    params['structureId'] = structureid;
                }
                else {
                    delete params['structureId'];
                }
                loader(obj, params);
            });

            $target.find(".modal-content").html($manager);
        });
    };

    $button.each(function () {
        var $sId = $(this).closest('.file-manager-selector').find('.file-manager-selector-id');
        loadItem($sId, $sId.val());
    });

    $button.click(function () {
        var params = {};
        params['type'] = $(this).data('filetype');
        loader($(this), params);
    });
});
