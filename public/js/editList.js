// JS for for of editing list
// Add and delete users for sharing

var collectionHolder = $('ul.users');
var addUserButton = $('<button type="button" id="add_subtask_link" class="btn btn-outline-success">Add share user</button>');
var newUserLi = $('<li></li>').append(addUserButton);

$(() => {
  // Set index for future elements
  collectionHolder.data('index', collectionHolder.find(':input').length);

  // Add event for adding user button
  addUserButton.on('click', () => {
    addUserForm();
  })

  // Add delete button to the end of each li
  collectionHolder.find('li').each(function (index) {
    $(this).children().append(createDeleteButton());
  });

  // Add new li element in the end with add button
  collectionHolder.append(newUserLi);
})

/**
 * Add user form to the list
 */
function addUserForm() {
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
  newUserLi.before(newLi);
}

/**
 * Add button to form in li. Li object has to the parent
 */
function createDeleteButton() {
  var deleteUserButton = $('<div><button type="button" class="remove-user btn btn-danger">X</button></div>');

  // Add click event for button
  deleteUserButton.on('click', () => {
    deleteUserButton.parent().remove();
  });

  return deleteUserButton;
}