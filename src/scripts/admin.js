import '../styles/admin.scss';

(function ($) {
  const form = $('#widen-media')
  const formSubmitButton = $('#widen-search-submit')
  const formSpinner = $('#widen-search-spinner')
  const searchResults = $('#widen-search-results')
  const paginationButton = $('.pagination-links .button')
  const addToLibrary = $('.add-to-library')

  form.submit(e => {
    startSpinner()
  })

  paginationButton.click(e => {
    startSpinner()
  })

  addToLibrary.click(function (e) {
    e.preventDefault()

    const item = $(this).attr('data-item')

    $.ajax({
      url: widen_media.ajax_url,
      type: 'POST',
      data: {
        action: 'widen_media_add_to_library',
        nonce: widen_media.ajax_nonce,
        item,
      },
    }).done(response => {
      console.log(response)
    })
  })

  function startSpinner() {
    formSubmitButton.attr('disabled', true)
    formSpinner.addClass('is-active')
    searchResults.addClass('disabled', true)
  }
}(jQuery))
