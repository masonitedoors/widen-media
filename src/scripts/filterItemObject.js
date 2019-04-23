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
  const {
    id,
    metadata,
    external_id: externalId,
    filename,
    file_upload_date: uploadDate,
    file_properties: fileProperties,
  } = rawObj

  const item = {}
  const baseImageUrl = 'https://embed.widencdn.net/img/masonite'
  const kb = fileProperties.size_in_kbytes
  const fileroot = filename.split('.').slice(0, 1)

  item.id = id
  item.description = typeof metadata.fields.description === 'undefined' ? '' : metadata.fields.description.toString()
  item.externalId = externalId
  item.filename = filename
  item.uploadDate = uploadDate
  item.fileSize = getFileSize(kb)
  item.fileFormat = fileProperties.format
  item.fileFormatType = fileProperties.format.type
  item.imageUrl = item.fileFormatType === 'image'
    ? null
    : (item.imageUrl = {
      skeleton: `${baseImageUrl}/${externalId}/50x50px/${fileroot}.png?crop=no`,
      thumbnail: `${baseImageUrl}/${externalId}/500x500px@2x/${fileroot}.png?crop=no`,
      exact: `${baseImageUrl}/${externalId}/exact/${fileroot}.png`,
    })

  console.log(rawObj, item)

  return item
}

export default filterItemObject
