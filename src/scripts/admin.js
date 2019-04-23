import '../styles/admin.scss'
import filterItemObject from './filterItemObject';

(function ($) {
  const form = $('#widen-media')
  const formInput = $('#widen-search-input')
  const formSubmitButton = $('#widen-search-submit')
  const formSpinner = $('#widen-search-spinner')
  const formResults = $('#widen-search-results')
  const totalItems = $('#widen-total-items')

  /**
   * Search media form submission.
   */
  form.submit(e => {
    e.preventDefault()

    const query = $('[name="swiden"]').val()

    startSpinner()
    resetResults()

    $.ajax({
      url: WIDEN_MEDIA_OBJ.ajax_url,
      type: 'POST',
      data: {
        action: 'widen_media_form_submit',
        nonce: WIDEN_MEDIA_OBJ.ajax_nonce,
        query,
      },
    }).done(response => {
      console.table(response)

      const { items } = response.data
      const itemCount = response.data.total_count

      if (items.length < 1) {
        formResults
          .find('.tiles')
          .append(`<p class="no-results">No results for <strong>${query}</strong></p>`)
      } else {
        totalItems.html(`<strong>${itemCount}</strong> results for <strong>${query}</strong>`)

        items.forEach(itemObj => {
          const item = filterItemObject(itemObj)
          const itemStr = JSON.stringify(item)
          const tile = $(`
            <li class="tile" id="${item.id}">
              <a
                data-fancybox
                data-src="#modal-${item.id}"
                href="javascript:;"
                title="${item.filename}"
                data-options='{"toolbar": false, "arrows": true, "autoFocus": false,"touch": false}'
              >
                <div class="tile__header" aria-hidden="true">
                  <img class="tile__image lazyload blur-up" src="${
  item.imageUrl.skeleton
  }" data-src="${item.imageUrl.thumbnail}" alt="" />
                </div>
                <div class="tile__content">
                  <p class="tile__title">${item.filename}</p>
                </div>
              </a>

              <form class="tile-modal" id="modal-${item.id}" style="display:none">
                <div class="modal__header">
                  <p class="modal__title">Widen Asset Details</p>
                </div>

                <div class="modal__grid">
                  <div class="modal__image-wrapper">
                    <img class="modal__image lazyload" data-src="${item.imageUrl.exact}" alt="" />
                  </div>

                  <div class="modal__meta">
                    <div class="details">
                      <div class="filename"><strong>File name:</strong> ${item.filename}</div>
                      <div class="format"><strong>File type:</strong> ${item.fileFormat}</div>
                      <div class="uploaded"><strong>Uploaded on:</strong> ${item.uploadDate}</div>
                      <div class="file-size"><strong>File size:</strong> ${item.fileSize}</div>
                      <br>
                      <p class="description">${item.description}</p>
                    </div>
                    <input class="item__data" type="hidden" value='${itemStr}'>
                    <button class="button button-primary add-to-media-library">Add To Media Library</button>
                </div>
              </form>
            </li>
          `)

          formResults.find('.tiles').append(tile)
        })
      }

      stopSpinner()

      $('.tile-modal').submit(function (e) {
        e.preventDefault()

        const item = $(this)
          .find('.item__data')
          .val()

        $.ajax({
          url: WIDEN_MEDIA_OBJ.ajax_url,
          type: 'POST',
          data: {
            action: 'widen_media_add_to_library',
            nonce: WIDEN_MEDIA_OBJ.ajax_nonce,
            item,
          },
        }).done(response => {
          $('.fancybox-close-small').trigger('click')
        })
      })
    })
  })

  function startSpinner() {
    formInput.attr('disabled', true)
    formSubmitButton.attr('disabled', true)
    formSpinner.addClass('is-active')
  }

  function stopSpinner() {
    formInput.attr('disabled', false)
    formSubmitButton.attr('disabled', false)
    formSpinner.removeClass('is-active')
  }

  function resetResults() {
    formResults.find('.tiles').html('')
  }
}(jQuery_3_4_0))
