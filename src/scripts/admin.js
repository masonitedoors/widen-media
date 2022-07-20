import '../styles/admin.scss';

(function ($) {
  const form = $('#widen-media')
  const paginationButton = $('.pagination-links .button')
  const addToLibrary = $('.add-to-library')
  const saveCollection = $('#widen-save-collection')

  form.submit(() => {
    startSearchSpinner()
  })

  paginationButton.click(() => {
    startSearchSpinner()
  })

  addToLibrary.click(function (e) {
    e.preventDefault()

    const btnAdd = $(this)

    startAddItemSpinner(btnAdd)

    let data = {}

    const type = $(this).attr('data-type')
    const format = $(this).attr('data-format')
    const ext = $(this).attr('data-ext')
    const id = $(this).attr('data-id')
    const filename = $(this).attr('data-filename')
    const description = $(this).attr('data-description')
    const url = $(this).attr('data-url')
    const templatedUrl = $(this).attr('data-templated-url')
    const thumbnailUrl = $(this).attr('data-thumbnail-url')
    const fields = $(this).attr('data-fields')

    // Create our data based on the asset type.
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
          templatedUrl,
          fields,
        }
        break
      case 'pdf':
        data = {
          action: 'widen_media_add_image_to_library',
          nonce: widen_media.ajax_nonce,
          type,
          id,
          filename,
          description,
          url,
          templatedUrl,
          thumbnailUrl,
          fields,
        }
        break
      case 'office':
        data = {
          action: 'widen_media_add_office_file_to_library',
          nonce: widen_media.ajax_nonce,
          type,
          ext,
          id,
          filename,
          description,
          url,
          templatedUrl,
          thumbnailUrl,
          fields,
        }
        break
      case 'generic':
        data = {
          action: 'widen_media_add_generic_file_to_library',
          nonce: widen_media.ajax_nonce,
          type,
          ext,
          id,
          filename,
          description,
          url,
          templatedUrl,
          thumbnailUrl,
          fields,
        }
        break
      case 'video':
        data = {
          action: 'widen_media_add_video_to_library',
          nonce: widen_media.ajax_nonce,
          type,
          format,
          id,
          filename,
          description,
          url,
          templatedUrl,
          thumbnailUrl,
          fields,
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
      window.location.reload()
    })
  })

  saveCollection.click(e => {
    e.preventDefault()

    const query = $('[name="prev_search"]').val()
    const items = $('#widen_image_query_data').html()

    const data = {
      action: 'widen_media_save_collection',
      nonce: widen_media.ajax_nonce,
      query,
      items,
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
      window.location.reload()
    })
  })

  /**
   * Start the spinner next to the main search input.
   */
  function startSearchSpinner() {
    $('#widen-search-submit').attr('disabled', true)
    $('#widen-search-spinner').addClass('is-active')
    $('#widen-search-results').addClass('disabled', true)
  }

  /**
   * Start the spinner for an individual result when adding
   * a new attachment to the WordPress Media Library.
   *
   * @param Object button The button of the card that was just clicked.
   */
  function startAddItemSpinner(button) {
    const tile = button.closest('.tile')
    const spinner = tile.find('.spinner')

    button.attr('disabled', true)
    tile.addClass('disabled', true)
    spinner.addClass('is-active')
  }
}(jQuery))
