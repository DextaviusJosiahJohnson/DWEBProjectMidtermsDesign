//this file contains the js navigation of the webapp

window.toggleMenu = function () {
  const sidebar = document.getElementById('sidebar');
  if (!sidebar) return;
  sidebar.classList.toggle('show');
};

window.goTo = function (page) {
  window.location.href = page;
};
