// JS for main page

$(() => {
  // Create click event for every button of remove task. Action processes through JSON requests.
  $('button.remove-task').each(function () {
    $(this).on('click', function () {
      $.post({
        url: '/task/delete/' + $(this).data('taskId'),
        dataType: 'json',
        success: (data, textStatus) => {
          if (data.code == 0) {
            $(this).parent().parent().remove()
          } else {
            console.log(data.message);
          }
        }
      });
    });
  });

  // Create click event for every checkbox of task. Action processes through JSON requests.
  $('input.check-task').each(function () {
    $(this).on('click', function () {
      var sendData = {
        id: $(this).data('taskId'),
        isDone: $('input.check-task').is(':checked')
      };
      $.post({
        url: '/task/check/' + $(this).data('taskId'),
        data: JSON.stringify(sendData),
        dataType: 'json',
        success: (data, textStatus) => {
          if (data.code == 0) {
            if (sendData.isDone) {
              $('ul#done-tasks').append($(this).parent().parent().parent().parent());
            } else {
              $('ul#undone-tasks').append($(this).parent().parent().parent().parent());
            }
          } else {
            console.log(data.message);
          }
        }
      });
    });
  });
});

