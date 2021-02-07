jQuery("document").ready(function () {
  jQuery("#emailVerification").on('click', e => {
    let container = jQuery("#emailVerContainer");
    jQuery.ajax({
      type: "POST",
      url: "/user/send-email",
    }).done((data) => {
      let html = `<div class="alert alert-success" role="alert">${data}</div>`;
      jQuery("#emailVerContainer").html(html)
    });
  })
});
