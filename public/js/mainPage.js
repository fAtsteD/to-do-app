// JS for main page

$(() => {
  $('button.remove-task').each(function() {$(this).on('click', function() {
    console.log($(this).data('taskId'));
    $.post({
      url: '/delete/' + $(this).data('taskId'),
      dataType: 'json',
      success: (data, textStatus) => {
        console.log(data);
        console.log($(this))
        if (data.code == 0) {
          $(this).parent().parent().remove()
        } else {
          console.log(data);
        }
      }
    })
  })});
});

