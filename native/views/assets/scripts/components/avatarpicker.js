document.addEventListener('alpine:init', () => {
  Alpine.data('avatar_picker', () => ({
    avatar: null,
    submitOnChange: false,
    endpoint: null,
    loading: false,
    useS3: false,

    init() {
      const value = this.$el.getAttribute('data-default');
      const submitOnChange = this.$el.getAttribute('data-submit');
      const endpoint = this.$el.getAttribute('data-endpoint');
      const useS3 = this.$el.getAttribute('data-s3');

      this.avatar = value ? value : null;
      this.submitOnChange = submitOnChange ? true : false;
      this.endpoint = endpoint ? endpoint : null;
      this.useS3 = useS3 ? true : false;
    },

    triggerFilePicker() {
      this.$refs.filePicker.click();
    },

    updatePreview(file) {
      if (!file) {
        return;
      }

      const reader = new FileReader();
      reader.onload = (e) => {
        this.avatar = e.target.result;

        if (this.submitOnChange) this.upload();
      };
      reader.readAsDataURL(file);
    },

    clearPreview() {
      this.avatar = null;
      this.$refs.filePicker.value = null;

      if (this.submitOnChange) this.delete();
    },

    async upload() {
      this.loading = true;

      // @TODO Tester upload pas S3
      if (this.useS3)
        await api.s3.upload(this.endpoint, this.$refs.filePicker.files[0]);
      else
        await api.fetch(this.endpoint, {
          method: 'POST',
          body: this.$refs.filePicker.files[0],
          isFile: true,
        });

      this.loading = false;
    },

    async delete() {
      this.loading = true;

      await api.fetch(this.endpoint, { method: 'DELETE' });

      this.loading = false;
    },
  }));
});
