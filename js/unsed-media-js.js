jQuery(document).ready(function ($) {
  // Handle delete button click
  $('.ikonic-umc-delete').on('click', function (event) {
    event.preventDefault();

    if (!confirm('Are you sure you want to delete this media item?')) {
      return;
    }

    var mediaId = $(this).data('id');
    var nonce = ajax_params.nonce;
    var button = $(this);

    $.ajax({
      url: ajax_params.ajax_url,
      type: 'POST',
      data: {
        action: 'ikonic_delete_media',
        media_id: mediaId,
        nonce: nonce
      },
      success: function (response) {
        if (response.success) {
          button.closest('tr').fadeOut(function () {
            $(this).remove();
          });
        } else {
          alert('Failed to delete media item.');
        }
      },
      error: function () {
        alert('An error occurred while deleting the media item.');
      }
    });
  });
});
