import '../styles/admin.scss';

(function ($) {
  const form = $('#widen-media')
  const formSubmitButton = $('#widen-search-submit')
  const formSpinner = $('#widen-search-spinner')
  const searchResults = $('#widen-search-results')

  form.submit(e => {
    startSpinner()
  })

  function startSpinner() {
    formSubmitButton.attr('disabled', true)
    formSpinner.addClass('is-active')
    searchResults.addClass('disabled', true)
  }
}(jQuery))
