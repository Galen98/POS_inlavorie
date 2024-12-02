function setMode(mode) {
    const body = document.body;
    const forminput = document.querySelectorAll('.form-inputs');
    const btn = document.querySelectorAll('#btn-action');
    const cardplan = document.querySelectorAll('#card-plan');
    const btnprimer = document.querySelectorAll('#btn-action-primer');
    const footer = document.getElementById('footers');
    const table = document.getElementById('table');
    const modeButton = document.getElementById('modeButton');
    const modeIcon = document.getElementById('modeIcon');
    const forms = document.getElementById('forms');
    const navside = document.getElementById('sidenavAccordion');

    if (mode === 'dark') {
    if (cardplan.length > 0) {
        cardplan.forEach(cardplan => {
            cardplan.classList.remove('bg-light')
            cardplan.classList.add('bg-dark')
        })
    }
    if (forminput.length > 0) {
        forminput.forEach(forminput => {
        forminput.classList.remove('bg-light');
        forminput.classList.remove('text-dark');
        forminput.classList.add('bg-dark');
        forminput.classList.add('text-light');
    });
    }

    if(btn.length > 0) {
        btn.forEach(btn => {
        btn.classList.remove('btn-light');
        btn.classList.add('btn-secondary');
    })
    }

    if(btnprimer.length > 0) {
        btnprimer.forEach(btnprimer => {
        btnprimer.classList.remove('btn-dark');
        btnprimer.classList.add('btn-light');
    })
    }
        if(table !== null){
        table.classList.remove('table-light');
        table.classList.add('table-dark');
        }
        if(forms !== null){
        forms.classList.remove('bg-light');
        forms.classList.remove('text-dark');
        forms.classList.add('bg-dark');
        forms.classList.add('text-light');
        }
        footer.classList.remove('bg-light');
        footer.classList.add('bg-dark');
        body.classList.remove('bg-light');
        body.classList.remove('text-dark');
        body.classList.add('bg-dark');
        body.classList.add('text-light');
        navside.classList.remove('sb-sidenav-light');
        navside.classList.add('sb-sidenav-dark');
        modeButton.classList.remove('btn-light');
        modeButton.classList.add('btn-dark');
        //modeIcon.className = 'bi bi-moon'; 
        modeButton.innerHTML = '<i class="bi bi-moon"></i> Dark'; 
        localStorage.setItem('theme', 'dark');
    } else {
    if (cardplan.length > 0) {
        cardplan.forEach(cardplan => {
            cardplan.classList.remove('bg-dark')
            cardplan.classList.add('bg-light')
        })
    }

    if (forminput.length > 0) {
            forminput.forEach(forminput => {
            forminput.classList.remove('bg-dark');
            forminput.classList.remove('text-light');
            forminput.classList.add('bg-light');
            forminput.classList.add('text-dark');
        });
     }

     if(btnprimer.length > 0) {
        btnprimer.forEach(btnprimer => {
        btnprimer.classList.remove('btn-light');
        btnprimer.classList.add('btn-dark');
    })
    }

        if(btn.length > 0) {
            btn.forEach(btn => {
            btn.classList.remove('btn-secondary');
            btn.classList.add('btn-light');
        })
        }

        if(table !== null){
        table.classList.remove('table-dark');
        table.classList.add('table-light');
        }
        if(forms !== null){
        forms.classList.remove('bg-dark');
        forms.classList.remove('text-light');
        forms.classList.add('bg-light');
        forms.classList.add('text-dark');
        }
        footer.classList.remove('bg-dark');
        footer.classList.add('bg-light');
        body.classList.remove('bg-dark');
        body.classList.remove('text-light');
        body.classList.add('bg-light');
        body.classList.add('text-dark');
        navside.classList.remove('sb-sidenav-dark');
        navside.classList.add('sb-sidenav-light');
        modeButton.classList.remove('btn-dark');
        modeButton.classList.add('btn-light');
        //modeIcon.className = 'bi bi-sun'; 
        modeButton.innerHTML = '<i class="bi bi-sun"></i> Light';
        localStorage.setItem('theme', 'light');
    }
}
document.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        setMode(savedTheme);
    }
});