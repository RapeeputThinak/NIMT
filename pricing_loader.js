document.addEventListener('DOMContentLoaded', async () => {
  const pageKey = document.body.dataset.pricingPage;
  if (!pageKey) {
    return;
  }

  try {
    const response = await fetch('pricing_config.json', { cache: 'no-store' });
    if (!response.ok) {
      console.error('Unable to load pricing configuration:', response.statusText);
      return;
    }

    const config = await response.json();
    const pageConfig = config[pageKey];
    if (!pageConfig) {
      console.warn('No pricing section found for', pageKey);
      return;
    }

    document.querySelectorAll('[data-price-key]').forEach((element) => {
      const key = element.dataset.priceKey;
      if (!key) {
        return;
      }
      const value = pageConfig[key];
      if (value !== undefined) {
        element.textContent = value;
      }
    });
  } catch (error) {
    console.error('Pricing loader error:', error);
  }
});
