document.addEventListener('DOMContentLoaded', function () {
  var tables = document.querySelectorAll('table.table');
  tables.forEach(function (table) {
    var headers = [];
    var ths = table.querySelectorAll('thead th');
    ths.forEach(function (th) {
      headers.push((th.textContent || '').trim());
    });

    if (headers.length === 0) {
      return;
    }

    table.classList.add('responsive-table');
    var rows = table.querySelectorAll('tbody tr');
    rows.forEach(function (row) {
      var cells = row.querySelectorAll('td');
      cells.forEach(function (cell, idx) {
        if (!cell.getAttribute('data-label')) {
          cell.setAttribute('data-label', headers[idx] || 'Field');
        }
      });
    });
  });
});
