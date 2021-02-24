jQuery("document").ready(function () {
  jQuery("#emailVerification").on('click', e => {
    let container = jQuery("#emailVerContainer");
    jQuery.ajax({
      type: "POST",
      url: "/user/send-email",
    }).done((data) => {
      let html = '';
      console.log(data.code)
      if(data.code === 200) {
        html = `<div class="alert alert-success" role="alert">${data.message}</div>`;
      } else {
        html = `<div class="alert alert-danger" role="alert">${data.message}</div>`;
      }
      jQuery("#emailVerContainer").html(html)
    });
  })
});
