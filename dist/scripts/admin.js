!function(e){var t={};function a(i){if(t[i])return t[i].exports;var n=t[i]={i:i,l:!1,exports:{}};return e[i].call(n.exports,n,n.exports,a),n.l=!0,n.exports}a.m=e,a.c=t,a.d=function(e,t,i){a.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:i})},a.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},a.t=function(e,t){if(1&t&&(e=a(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var i=Object.create(null);if(a.r(i),Object.defineProperty(i,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var n in e)a.d(i,n,function(t){return e[t]}.bind(null,n));return i},a.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return a.d(t,"a",t),t},a.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},a.p="",a(a.s=0)}([function(e,t,a){"use strict";a(1),function(e){var t=e("#widen-media"),a=e(".pagination-links .button"),i=e(".add-to-library"),n=e("#widen-save-collection");function r(){e("#widen-search-submit").attr("disabled",!0),e("#widen-search-spinner").addClass("is-active"),e("#widen-search-results").addClass("disabled",!0)}t.submit(function(){r()}),a.click(function(){r()}),i.click(function(t){t.preventDefault();var a,i,n,r=e(this);i=(a=r).closest(".tile"),n=i.find(".spinner"),a.attr("disabled",!0),i.addClass("disabled",!0),n.addClass("is-active");var d={},o=e(this).attr("data-type"),l=e(this).attr("data-id"),c=e(this).attr("data-filename"),u=e(this).attr("data-description"),s=e(this).attr("data-url"),f=e(this).attr("data-thumbnail-url"),_=e(this).attr("data-templated-url"),p=e(this).attr("data-pager-url"),m=e(this).attr("data-fields");switch(o){case"image":d={action:"widen_media_add_image_to_library",nonce:widen_media.ajax_nonce,type:o,id:l,filename:c,description:u,url:s,thumbnailUrl:f,templatedUrl:_,pagerUrl:p,fields:m};break;case"pdf":d={action:"widen_media_add_pdf_to_library",nonce:widen_media.ajax_nonce,type:o,id:l,filename:c,description:u,url:s};break;case"audio":d={action:"widen_media_add_audio_to_library",nonce:widen_media.ajax_nonce,type:o,id:l,filename:c,description:u,url:s}}e.ajax({url:widen_media.ajax_url,type:"POST",data:d}).done(function(e){window.location.reload()})}),n.click(function(t){t.preventDefault();var a=e('[name="prev_search"]').val(),i=e("#widen_image_query_data").html(),n={action:"widen_media_save_collection",nonce:widen_media.ajax_nonce,query:a,items:i};e.ajax({url:widen_media.ajax_url,type:"POST",data:n}).done(function(e){window.location.reload()})})}(jQuery)},function(e,t,a){}]);