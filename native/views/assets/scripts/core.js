/**
 * Runs before Alpine initializiation
 * -> Creates an `ezekiel` object attached to the `window` superglobal
 * -> Creates a method `ezekiel.makeForm` that helps working with forms and async within Alpine
 *
 * -> Creates an `api` object attached to the `window` superglobal
 * -> Creates a method `api.fetch` that works like native `fetch` with default settings
 *
 */
(() => {
  window.ezekiel = {};

  window.ezekiel.makeForm = (alpineForm) => {
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
       * Boolean to determine whether the form was touched or not, based on payload
       */
      dirty: false,

      /**
       * Boolean to manually block natural update of payload-dependent members (only 'dirty' so far)
       */
      locked: false,

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
       * Determines whether manual validation should be applied
       */
      _hasCustomValidation: false,

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
        // Setup form dirty as well
        instance.$watch('payload', () => {
          if (instance.locked) return;

          if (instance._hasCustomValidation)
            instance.ready = instance._performValidation(
              instance.payload,
              instance._schema
            );
          else if (Iodine)
            instance.ready = Iodine.isValidSchema(
              instance.payload,
              instance._schema
            );

          instance.dirty = true;
        });

        // Setup default values injected in DOM via value="xxx"
        [...instance.$el.querySelectorAll('input, select, textarea')].forEach(
          (i) => {
            const name = i.getAttribute('name');
            const value = i.value;

            if (!name) return;
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

        // lock method
        instance.lock = () => {
          instance.locked = true;
        };

        // Unlock method
        instance.unlock = () => {
          setTimeout(() => {
            instance.locked = false;
          }, 500);
        };

        // resetPayload method
        instance.resetPayload = () => {
          instance.payload = Object.keys(instance._schema).reduce(
            (a, b) => ({ ...a, [b]: '' }),
            {}
          );

          [...instance.$el.querySelectorAll('input, select, textarea')].forEach(
            (i) => {
              const name = i.getAttribute('name');
              const value = i.value;

              if (!name) return;
              if (!value) return;

              instance.payload[name] = value;
            }
          );
        };

        // erasePayload method
        instance.erasePayload = () => {
          instance.payload = Object.keys(instance._schema).reduce(
            (a, b) => ({ ...a, [b]: '' }),
            {}
          );
        };
      },
    };

    return { ...defaultForm, ...alpineForm };
  };
  window.ezekiel.triggerAlert = (alertId) => {
    document.body.dispatchEvent(
      new CustomEvent('alert', { detail: { id: alertId }, bubbles: true })
    );
  };

  window.api = {};
  // window.api.root = '/a';
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

  // S3 API methods
  window.api.s3 = {};

  // Process for uploading to S3 is :
  // 1. Request presigned upload url
  // 2. Upload to S3
  // 3. Confirm upload to backend
  // NB: Step 1 and 3 happen on the same API endpoint, and 2 on S3
  window.api.s3.upload = async (endpoint, file, payload = {}) => {
    // Get upload url
    let response = await api.fetch(endpoint, {
      method: 'POST',
      body: { file: file.name, ...payload },
    });

    // Upload to the url we just got
    await fetch(response.content.upload_url, {
      method: 'PUT',
      body: file,
    });

    // Send confirmation to back end that we succesfully uploaded to S3
    response = await api.fetch(endpoint, {
      method: 'POST',
      body: { file: file.name, uploaded: true, ...payload },
    });

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
      nodes[i].addEventListener('click', (evt) => {
        const link = document.createElement('a');
        link.setAttribute('href', nodes[i].getAttribute('data-href'));

        if (evt.ctrlKey || evt.metaKey) link.setAttribute('target', '_blank');

        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        // window.location = nodes[i].getAttribute('data-href');
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
