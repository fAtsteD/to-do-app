// JS for form of edit task
// Add and delete subtasks.

var collectionHolder = $('ul.subtasks');
var addSubtasksButton = $('<button type="button" id="add_subtask_link" class="btn btn-outline-success">Add a subtask</button>');
var newLinkLi = $('<li></li>').append(addSubtasksButton);

$(() => {
  collectionHolder.append(newLinkLi);
  collectionHolder.data('index', collectionHolder.find(':input').length);

  addSubtasksButton.on('click', () => {
    addSubtaskForm(collectionHolder, newLinkLi);
  });
  
  // Add event click to all buttons for deleting existing subtasks
  $('button.remove-subtask').each(function (index) {
    $(this).on('click', function () {
      $(this).parent().parent().parent().remove();
    });
  });
});

/**
 * Add subtask form to the list in form of edit task
 * @param  $collectionHolder 
 * @param newLinkLi 
 */
function addSubtaskForm(collectionHolder, newLinkLi) {
  var prototype = collectionHolder.data('prototype');
  var index = collectionHolder.data('index');
  var newForm = prototype;

  newForm = newForm.replace(/__name__/gm, index);
  collectionHolder.data('index', index + 1);
  var newFormLi = $('<li></li>').append(newForm);
  addDeleteLinkSubtask(newFormLi.find('div.form-row'));
  newLinkLi.before(newFormLi);
}

/**
 * Add link for remove subtask
 * @param subtaskFormLi
 */
function addDeleteLinkSubtask(subtaskFormLi) {
  var removeButton = $('<div class="col-1"><button type="button" class="remove-subtask btn btn-danger">X</button></div>');
  subtaskFormLi.append(removeButton);

  removeButton.on('click', () => {
    subtaskFormLi.parent().remove();
  });
}

function deleteTask() {
  
}