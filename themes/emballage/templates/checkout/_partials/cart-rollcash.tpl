{if $cart->getCustomer() and $cart->getCustomer()->rollcash}
  <div class="alert alert-warning text-info text-center">
    <h3 class="">{l s="Rollcash"}</h3>
    <hr />
    {l s='Vous avez accumulé [1]%s HT[/1] dans votre cagnote Rollcash et pouvez convertir ce montant en réduction pour votre prochaine commande.' sprintf=[Tools::displayPrice($cart->getCustomer()->rollcash)] tags=['<b>']}
    <form method="post">
      <button type="submit" id="convert" name="use_rollcash" class="btn btn-warning bold mt-1">
        {l s="Convertir mon Rollcash"}
      </button>
    </form>
  </div>
{/if}