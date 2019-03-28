// JS for form of edit task
// Add and delete subtasks.

var collectionHolder = $('ul.subtasks');
var addSubtasksButton = $('<button type="button" id="add_subtask_link" class="btn btn-outline-success">+</button>');
var newSubtaskLi = $('<li></li>').append(addSubtasksButton);

$(() => {
  // Set insex for future elements
  collectionHolder.data('index', collectionHolder.find(':input').length);

  // Add event for adding subtask button
  addSubtasksButton.on('click', () => {
    addSubtaskForm();
  });

  // Add delete delete button to the end of each li
  collectionHolder.find('li').each(function (index) {
    $(this).children().append(createDeleteButton());
  });

  // Add new li element in the end with add button
  collectionHolder.append(newSubtaskLi);
});

/**
 * Add subtask form to the list in form of edit task
 */
function addSubtaskForm() {
  var newForm = collectionHolder.data('prototype');
  var index = collectionHolder.data('index');

  // Add index to form
  newForm = newForm.replace(/__name__/gm, index);
  collectionHolder.data('index', index + 1);

  // Create new li and add new form
  var newLi = $('<li></li>').append(newForm);

  // Add delete button for form
  newLi.children().append(createDeleteButton());

  // Insert form in the end but in front of adding button
  newSubtaskLi.before(newLi);
}

/**
 * Add button to form in li. Li object has to the parent
 */
function createDeleteButton() {
  var deleteSubtaskButton = $('<div><button type="button" class="remove-subtask btn btn-danger">X</button></div>');

  // Add click event for button
  deleteSubtaskButton.children().on('click', () => {
    deleteSubtaskButton.parent().remove();
  });

  return deleteSubtaskButton;
}
