document.addEventListener('alpine:init', () => {
  Alpine.data('component_otpmodal', (id, endpoint, callback) => ({
    // Display
    id: id,
    open: false,
    loading: false,
    showOTPInput: false,

    // Form
    endpointOTP: endpoint,
    payload: { otp: '' },
    success: false,
    error: false,
    get ready() {
      return this.payload.otp.length === 6;
    },

    onSuccess: callback,

    async requestOTP() {
      this.loading = true;

      // Request a new OTP
      await api.fetch(this.endpointOTP, { method: 'POST' });
      this.loading = false;
      this.showOTPInput = true;
    },

    async submit() {
      this.loading = true;
      this.error = null;
      this.success = null;

      // Send OTP for verification
      const response = await api.fetch(this.endpointOTP, {
        method: 'POST',
        body: {
          otp: this.payload.otp,
        },
      });

      if (response.failed) {
        this.error = 'error';
        this.loading = false;
        return;
      }

      this.loading = false;
      this.success = 'success';

      setTimeout(() => {
        this.open = false;
        this.onSuccess();

        setTimeout(() => {
          this.payload.otp = '';
          this.showOTPInput = false;
          this.success = null;
          this.error = null;
          this.$dispatch('clear');
        }, 400);
      }, 800);
    },

    cancel() {
      this.open = false;

      if (this.showOTPInput)
        setTimeout(() => {
          this.showOTPInput = false;
          this.error = null;
          this.success = null;
        }, 400);
    },
  }));
});
