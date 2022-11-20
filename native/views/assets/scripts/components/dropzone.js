document.addEventListener('alpine:init', () => {
  Alpine.data('component_dropzone', (callback = null) => ({
    dragging: false,
    loadingUpload: false,
    callback: null,
    isMultiple: false,
    progress: 0,
    endpoint: null,
    useS3: false,
    accepted_formats: [],

    init() {
      if (this.$root.dataset.multiple) this.isMultiple = true;
      if (this.$root.dataset.s3) this.useS3 = true;

      if (this.$root.dataset.endpoint)
        this.endpoint = this.$root.dataset.endpoint;

      this.callback = callback;
      this.accepted_formats = JSON.parse(this.$root.dataset.accept);
    },

    triggerFilePicker() {
      if (this.loadingUpload) return;
      this.$refs.filePicker.click();
    },

    async handleFilePickerChange(file) {
      if (!file) return;

      this.loadingUpload = true;

      if (this.endpoint)
        if (this.useS3) await api.s3.upload(this.endpoint, file);

      this.progress = 100;

      this.callback([file]);

      setTimeout(() => {
        this.loadingUpload = false;
        this.progress = 0;
      }, 150);
    },

    handleDragEnter(event) {
      event.preventDefault();
      this.dragging = true;
    },

    handleDragLeave(event) {
      event.preventDefault();
      this.dragging = false;
    },

    // Required to have the "drop" event fire
    handledragOver(event) {
      event.preventDefault();
    },

    async handleDrop(event) {
      event.preventDefault();

      this.dragging = false;

      if (!event.dataTransfer.items) return (this.loadingUpload = false);

      const items = [...event.dataTransfer.items];
      const files = items
        .filter((i) => i.kind === 'file')
        .map((i) => i.getAsFile())
        .filter((f) => this.accepted_formats.some((af) => f.type === af));

      if (!files || files.length === 0) return;

      this.loadingUpload = true;
      const filesToUpload = this.isMultiple ? files : [files[0]];

      if (this.endpoint)
        for (let i = 0; i < filesToUpload.length; i++) {
          if (this.useS3) await api.s3.upload(this.endpoint, filesToUpload[i]);
          this.progress = Math.ceil((100 * (i + 1)) / filesToUpload.length);
        }

      this.callback(filesToUpload);

      setTimeout(() => {
        this.loadingUpload = false;
        this.progress = 0;
      }, 150);
    },
  }));
});
