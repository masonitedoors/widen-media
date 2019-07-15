import '../styles/admin.scss';

(function ($) {
  const form = $('#widen-media')
  const formSubmitButton = $('#widen-search-submit')
  const formSpinner = $('#widen-search-spinner')
  const searchResults = $('#widen-search-results')
  const paginationButton = $('.pagination-links .button')

  form.submit(e => {
    startSpinner()
  })

  paginationButton.click(e => {
    startSpinner()
  })

  function startSpinner() {
    formSubmitButton.attr('disabled', true)
    formSpinner.addClass('is-active')
    searchResults.addClass('disabled', true)
  }
}(jQuery))
