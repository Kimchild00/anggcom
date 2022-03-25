require('./bootstrap');

window.MODAL = {
    show(el){
        $(el).removeClass('hidden')
    },

    hide(el){
        $(el).addClass('hidden')
    }
}

/** 
 * how to use modal
 * 
 * $modal.show(ell) -> show modal
 * $modal.hide(ell) -> hide modal
 **/

$(function(){
    $('#user-menu-button').click(function(){
        if($('#profilDropdown').hasClass('hidden')){
            $('#profilDropdown').removeClass("hidden")
        }else{
            $('#profilDropdown').addClass("hidden")
        }
    })
    
    $('.sidebar-dialog').on('click', function(){
        if($('#sidebar').hasClass("hidden")){
            $('#sidebar').removeClass('hidden')
        }else{
            $('#sidebar').addClass('hidden')
        }
    })

    $('.close-modal').on('click', function(){
        MODAL.hide('.modal')
    })

    $('.tab-menu-list').on('click', function(){
        var target = $(this).data('target')
        $(this).addClass('bg-blue-200').siblings().removeClass('bg-blue-200').addClass('hover:bg-blue-200')
        $('.'+target).removeClass('hidden').siblings().addClass('hidden')
    })

    $('.submitUserModal').on('click', function(){
        $('#userForm').submit()
        MODAL.hide('.modal')
    })

    $('.newUser').on('click', function(){
        MODAL.show('.user-modal')
    })

    $('.newFinance').on('click', function(){
        MODAL.show('.finance-modal')
    })

    $('.submitFinanceModal').on('click', function(){
        $('#financeForm').submit()
        MODAL.hide('.finance-modal')
    })

})