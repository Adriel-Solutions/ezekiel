/**
 * Runs before Alpine initializiation
 * -> Creates an `adriel` object attached to the `window` superglobal
 * -> Creates a method `adriel.makeForm` that helps working with forms and async within Alpine
 *
 * -> Creates an `api` object attached to the `window` superglobal
 * -> Creates a method `api.fetch` that works like native `fetch` with default settings
 *
 */
(() => {
  window.adriel = {};

  window.adriel.makeForm = (alpineForm) => {
    const defaultForm = {
      /**
       * Set of rules to validate
       */
      _schema: {},

      /**
       * Data to send via POST / PUT, to be validated via _schema
       */
      payload: {},

      /**
       * Boolean to determine whether the form is submittable or not, based on _schema
       */
      ready: false,

      /**
       * Boolean to determine whether the form is loading or not, for UI aesthetic, and to prevent double submit
       */
      loading: false,

      /**
       * String determining what success message to show, often using "success" when there's only one message
       */
      success: null,

      /**
       * Same as `success` but for error
       */
      error: null,

      /**
       * Takes an instance of Alpine.data, and attaches variables and watchers to it
       */
      initForm: (instance) => {
        // Setup internal payload derived from the schema
        instance.payload = Object.keys(instance._schema).reduce(
          (a, b) => ({ ...a, [b]: '' }),
          {}
        );

        // Setup form validation when payload changes (using magic $watch)
        if (!instance._hasCustomValidation)
          instance.$watch('payload', () => {
            if (!Iodine) return false;
            instance.ready = Iodine.isValidSchema(
              instance.payload,
              instance._schema
            );
          });
        else
          instance.$watch('payload', () => {
            instance.ready = instance._performValidation(
              instance.payload,
              instance._schema
            );
          });

        // Setup default values injected in DOM via value="xxx"
        [...instance.$el.querySelectorAll('input, select, textarea')].forEach(
          (i) => {
            const name = i.getAttribute('name');
            const value = i.value;

            if (!value) return;

            instance.payload[name] = value;
          }
        );

        // Perform initial schema validation in case there are default values already
        if (!instance._hasCustomValidation)
          instance.ready = Iodine.isValidSchema(
            instance.payload,
            instance._schema
          );
        else
          instance.ready = instance._performValidation(
            instance.payload,
            instance._schema
          );

        /**
         * Define `redirect` method, that can, post-submit, redirect to
         * URL defined by `data-redirect=URL`
         */
        instance.redirect = () => {
          const url = instance.$el.dataset.redirect;
          if (!url) return;
          window.location = url;
        };
      },
    };

    return { ...defaultForm, ...alpineForm };
  };

  window.api = {};
  window.api.root = '/a';
  window.api.fetch = async (endpoint, params) => {
    const headers = params.headers || {};

    if (!params.isFile) headers['Content-Type'] = 'application/json';

    const url = `${api.root}${endpoint}`;

    let response = await fetch(url, {
      ...params,
      headers,
      credentials: 'include',
      body: !params.isFile ? JSON.stringify(params.body) : params.body,
    }).then((r) => r.json());

    if (parseInt(response.code) > 399) response.failed = true;

    return response;
  };
})();

/**
 * Runs after Alpine initialization, when the page has fully loaded
 */
window.addEventListener('load', () => {
  /**
   * Make nodes with `data-href=URL` behave like <a href="URL"></a> tags
   */
  (() => {
    const nodes = document.querySelectorAll('[data-href]');

    if (!nodes) return;

    for (let i = 0; i < nodes.length; i++) {
      nodes[i].addEventListener('click', () => {
        window.location = nodes[i].getAttribute('data-href');
      });
    }
  })();

  /**
   * Appends an "quals" rule to Iodine
   */
  (() => {
    Iodine.addRule('equals', (value, param) => value?.toString() == param);
  })();
});
