function getFileSize(kb) {
  let _size = kb
  const fSExt = ['KB', 'MB', 'GB']
  let i = 0
  while (_size > 900) {
    _size /= 1024
    // eslint-disable-next-line no-plusplus
    i++
  }
  return `${Math.round(_size * 100) / 100} ${fSExt[i]}`
}

function filterItemObject(rawObj) {
  const item = {}
  const baseImageUrl = 'https://embed.widencdn.net/img/masonite'
  const kb = rawObj.file_properties.size_in_kbytes
  const fileroot = rawObj.filename.split('.').slice(0, 1)

  item.id = rawObj.id
  item.description = rawObj.metadata.fields.description.toString()
  item.externalId = rawObj.external_id
  item.filename = rawObj.filename
  item.uploadDate = rawObj.file_upload_date
  item.fileSize = getFileSize(kb)
  item.fileFormat = rawObj.file_properties.format
  item.imageUrl = {
    thumbnail: `${baseImageUrl}/${rawObj.external_id}/500x500px/${fileroot}.png?crop=no`,
    exact: `${baseImageUrl}/${rawObj.external_id}/exact/${fileroot}.png`,
  }

  return item
}

export default filterItemObject
