function parseCounterValue(value) {
  const numericValue = Number.parseFloat(value.replace(/[^0-9.-]/g, ''));

  return Number.isFinite(numericValue) ? numericValue : null;
}

function formatCounterValue(value, originalValue) {
  const roundedValue = Math.round(value).toLocaleString();

  if (originalValue.includes('$')) {
    return `$${roundedValue}`;
  }

  return roundedValue;
}

export function initializeDashboardAnimations() {
  const dashboard = document.querySelector('[data-dashboard]');

  if (!dashboard) {
    return;
  }

  const counters = [...dashboard.querySelectorAll('[data-dashboard-counter]')];
  const activityItems = [...dashboard.querySelectorAll('[data-dashboard-activity]')];
  const healthBars = [...dashboard.querySelectorAll('[data-dashboard-health]')];
  const chartLine = dashboard.querySelector('[data-dashboard-chart-line]');
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function showFinalState() {
    counters.forEach((counter) => {
      counter.textContent = counter.dataset.dashboardValue;
    });

    activityItems.forEach((item) => {
      item.classList.add('is-visible');
    });

    healthBars.forEach((bar) => {
      bar.style.width = `${bar.dataset.dashboardHealthValue}%`;
    });

    if (chartLine) {
      chartLine.classList.add('is-drawn');
    }
  }

  function animateCounter(counter) {
    const originalValue = counter.dataset.dashboardValue;
    const targetValue = parseCounterValue(originalValue);

    if (targetValue === null) {
      counter.textContent = originalValue;
      return;
    }

    const duration = 1200;
    const startTime = performance.now();

    function updateCounter(currentTime) {
      const elapsed = currentTime - startTime;
      const progress = Math.min(elapsed / duration, 1);
      const easedProgress = 1 - Math.pow(1 - progress, 3);

      counter.textContent = formatCounterValue(
        targetValue * easedProgress,
        originalValue
      );

      if (progress < 1) {
        window.requestAnimationFrame(updateCounter);
      } else {
        counter.textContent = originalValue;
      }
    }

    counter.textContent = formatCounterValue(0, originalValue);
    window.requestAnimationFrame(updateCounter);
  }

  function runAnimations() {
    counters.forEach((counter) => animateCounter(counter));

    activityItems.forEach((item, index) => {
      window.setTimeout(() => {
        item.classList.add('is-visible');
      }, index * 140);
    });

    healthBars.forEach((bar, index) => {
      window.setTimeout(() => {
        bar.style.width = `${bar.dataset.dashboardHealthValue}%`;
      }, 250 + index * 120);
    });

    if (chartLine) {
      chartLine.classList.add('is-drawn');
    }
  }

  if (reducedMotion || !('IntersectionObserver' in window)) {
    showFinalState();
    return;
  }

  let hasAnimated = false;

  const observer = new IntersectionObserver(
    (entries) => {
      const entry = entries[0];

      if (!entry.isIntersecting || hasAnimated) {
        return;
      }

      hasAnimated = true;
      runAnimations();
      observer.disconnect();
    },
    {
      threshold: 0.3
    }
  );

  observer.observe(dashboard);
}