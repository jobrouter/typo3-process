import DocumentService from '@typo3/core/document-service.js';
import RegularEvent from '@typo3/core/event/regular-event.js';

DocumentService.ready().then(() => {
  const tableElement = document.getElementById('jobrouter-process-links-table');

  if (!tableElement) {
    return;
  }

  new RegularEvent('click', event => {
    const processTableFieldsToggler = event.target.closest('.jobrouter-process-table-fields-count');

    if (!processTableFieldsToggler) {
      return;
    }

    const collapseElement = processTableFieldsToggler.querySelector('.jobrouter-process-table-fields-collapse');
    const expandElement = processTableFieldsToggler.querySelector('.jobrouter-process-table-fields-expand');
    const listElement = processTableFieldsToggler.parentNode.querySelector('.jobrouter-process-table-fields-list');

    if (!collapseElement || !expandElement || !listElement) {
      return;
    }

    if (collapseElement.style.display === 'none') {
      collapseElement.style.display = '';
      expandElement.style.display = 'none';
      listElement.style.display = 'none';
      return;
    }

    collapseElement.style.display = 'none';
    expandElement.style.display = '';
    listElement.style.display = '';
  }).bindTo(tableElement);
});
