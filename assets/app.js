import './bootstrap.js';
import './styles/app.css';
import TomSelect from 'tom-select';

// Wait until the DOM is fully loaded
document.addEventListener('DOMContentLoaded', () => {
  const selects = document.querySelectorAll('.menu-search');
  selects.forEach(select => {
    new TomSelect(select, {
      create: false,
      sortField: { field: "text", direction: "asc" },
      placeholder: "Search or select a dish (optional)",
    });
  });
});
