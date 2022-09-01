document.addEventListener('alpine:init', () => {
  Alpine.data('component_otp', (length) => ({
    otp_value: '',
    otp_length: length,

    handleInput(e) {
      e.preventDefault();
      const input = e.target;

      // Force uppercase
      if (input.value) input.value = input.value.toUpperCase();

      // Rebuild the whole OTP on every change
      let new_otp_value = '';
      for (let i = 0; i < this.otp_length; i++)
        new_otp_value += this.$refs[`otp_input_${i + 1}`].value.toUpperCase();

      this.otp_value = new_otp_value;

      // Trigger submit if the last input was filled
      if (
        input.value &&
        input.getAttribute('x-ref') === `otp_input_${this.otp_length}`
      ) {
        // This has to be run after input validation, hence the timeout
        setTimeout(() => {
          document.querySelector('form button[type="submit"]').click();
        }, 250);
      }

      // Focus the next input when possible
      if (!input.nextElementSibling || !input.value) return;

      input.nextElementSibling.focus();
      input.nextElementSibling.select();
    },

    handlePaste(e) {
      e.preventDefault();

      let paste = e.clipboardData.getData('text');

      if (!paste) return;

      // Shorten paste if needed
      if (paste.length > this.otp_length)
        paste = paste.substr(0, this.otp_length);

      // Update internal OTP
      this.otp_value = paste;

      // Update inputs
      for (let i = 0; i < paste.length; i++)
        this.$refs[`otp_input_${i + 1}`].value = paste[i];

      // Blur when all fields are set
      if (paste.length >= this.otp_length) document.activeElement.blur();
      // Focus the next input when paste is too short to fill everything
      else this.$refs[`otp_input_${paste.length + 1}`].focus();

      // Auto-submit if input is full
      if (paste.length >= this.otp_length)
        setTimeout(() => {
          document.querySelector('form button[type="submit"]').click();
        }, 250);
    },

    handleBackspace(index) {
      const current = this.$refs[`otp_input_${index + 1}`];

      if (current.value) {
        current.value = '';
        return;
      }

      const previous = this.$refs[`otp_input_${index}`];

      if (!previous) document.activeElement.blur();
      else previous.focus();
    },
  }));
});
