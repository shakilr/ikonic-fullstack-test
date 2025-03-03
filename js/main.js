jQuery(document).ready(function ($) {
  // Handle project filtering via AJAX
  $('.project-filters').on('submit', function (event) {
    event.preventDefault();

    var start_date = $('#start-date').val();
    var end_date = $('#end-date').val();
    var nonce = ajax_params.nonce;

    console.log(start_date);
    console.log(end_date);
    console.log(nonce);

    // Show loading indicator
    $('.project-list').html('');
    $('.loading-indicator').show();

    $.ajax({
      url: ajax_params.ajax_url,
      type: 'POST',
      data: {
        action: 'filter_projects',
        start_date: start_date,
        end_date: end_date,
        nonce: nonce
      },
      success: function (response) {
        // Hide loading indicator
        $('.loading-indicator').hide();

        // Update project list with the response
        $('.project-list').html(response);
      },
      error: function () {
        // Hide loading indicator on error
        $('.loading-indicator').hide();
        $('.project-list').html('<p>An error occurred while fetching the projects. Please try again.</p>');
      }
    });
  });
});
