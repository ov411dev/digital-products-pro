export function initializeWorkflowShowcase() {
  const showcase = document.querySelector('[data-workflow-showcase]');

  if (!showcase) {
    return;
  }

  const dataElement = showcase.querySelector('[data-workflow-data]');
  const buttons = [...showcase.querySelectorAll('[data-workflow-step]')];
  const panel = showcase.querySelector('[data-workflow-panel]');
  const status = showcase.querySelector('[data-workflow-status]');
  const title = showcase.querySelector('[data-workflow-title]');
  const duration = showcase.querySelector('[data-workflow-duration]');
  const description = showcase.querySelector('[data-workflow-description]');
  const actions = showcase.querySelector('[data-workflow-actions]');
  const progress = showcase.querySelector('[data-workflow-progress]');

  if (!dataElement || !panel || !status || !title || !duration || !description || !actions) {
    return;
  }

  let workflowSteps;

  try {
    workflowSteps = JSON.parse(dataElement.textContent);
  } catch {
    return;
  }

  function selectStep(index, moveFocus = false) {
    const step = workflowSteps[index];

    if (!step) {
      return;
    }

    buttons.forEach((button, buttonIndex) => {
      const active = buttonIndex === index;

      button.classList.toggle('is-active', active);
      button.setAttribute('aria-selected', String(active));
      button.setAttribute('tabindex', active ? '0' : '-1');

      if (active && moveFocus) {
        button.focus();
      }
    });

    panel.setAttribute('aria-labelledby', `workflow-tab-${step.id}`);
    status.textContent = step.status;
    title.textContent = step.title;
    duration.textContent = step.duration;
    description.textContent = step.description;

    actions.replaceChildren(
      ...step.actions.map((action) => {
        const item = document.createElement('li');
        const icon = document.createElement('span');

        icon.setAttribute('aria-hidden', 'true');
        icon.textContent = '✓';

        item.append(icon, document.createTextNode(action));

        return item;
      })
    );

    if (progress) {
      progress.style.width = `${((index + 1) / workflowSteps.length) * 100}%`;
    }
  }

  buttons.forEach((button, index) => {
    button.addEventListener('click', () => selectStep(index));

    button.addEventListener('keydown', (event) => {
      let nextIndex = index;

      if (event.key === 'ArrowDown' || event.key === 'ArrowRight') {
        nextIndex = (index + 1) % buttons.length;
      } else if (event.key === 'ArrowUp' || event.key === 'ArrowLeft') {
        nextIndex = (index - 1 + buttons.length) % buttons.length;
      } else if (event.key === 'Home') {
        nextIndex = 0;
      } else if (event.key === 'End') {
        nextIndex = buttons.length - 1;
      } else {
        return;
      }

      event.preventDefault();
      selectStep(nextIndex, true);
    });
  });
}