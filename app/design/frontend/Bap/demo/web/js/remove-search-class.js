define(['jquery'], function ($) {
  'use strict';
  $(document).ready(function () {
      $(document).on('focus mouseover', '.right-block-search', function () {
          $(this).removeClass('ui-menu-item');
      });
  });
});
