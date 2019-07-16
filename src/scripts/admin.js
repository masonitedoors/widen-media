import '../styles/admin.scss';

(function ($) {
  const form = $('#widen-media')
  const formSubmitButton = $('#widen-search-submit')
  const formSpinner = $('#widen-search-spinner')
  const searchResults = $('#widen-search-results')
  const paginationButton = $('.pagination-links .button')
  const addToLibrary = $('.add-to-library')
  const currentUrl = window.location.href

  form.submit(e => {
    startSpinner()
  })

  paginationButton.click(e => {
    startSpinner()
  })

  addToLibrary.click(function (e) {
    e.preventDefault()

    let data = {}

    const type = $(this).attr('data-type')
    const id = $(this).attr('data-id')
    const filename = $(this).attr('data-filename')
    const description = $(this).attr('data-description')
    const url = $(this).attr('data-url')

    switch (type) {
      case 'image':
        data = {
          action: 'widen_media_add_image_to_library',
          nonce: widen_media.ajax_nonce,
          type,
          id,
          filename,
          description,
          url,
        }
        break
      case 'pdf':
        data = {
          action: 'widen_media_add_pdf_to_library',
          nonce: widen_media.ajax_nonce,
          type,
          id,
          filename,
          description,
          url,
        }
        break
      case 'audio':
        data = {
          action: 'widen_media_add_audio_to_library',
          nonce: widen_media.ajax_nonce,
          type,
          id,
          filename,
          description,
          url,
        }
        break
      default:
    }

    /**
     * Make the ajax request via WordPress.
     *
     * @see https://developer.wordpress.org/plugins/javascript/ajax/
     */
    $.ajax({
      url: widen_media.ajax_url,
      type: 'POST',
      data,
    }).done(response => {
      console.log(response)
      console.log(currentUrl)
    })
  })

  function startSpinner() {
    formSubmitButton.attr('disabled', true)
    formSpinner.addClass('is-active')
    searchResults.addClass('disabled', true)
  }
}(jQuery))
