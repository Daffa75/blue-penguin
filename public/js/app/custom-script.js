document.addEventListener('livewire:initialized', () => {
    window.Apex = {
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + ' Days';
                }
            }
        }
    }
});