{capture name=getName assign=userName}{sugar_fetch object=$parentFieldArray key=$nameField}{/capture}
{capture name=getId assign=userId}{sugar_fetch object=$parentFieldArray key=$idField}{/capture}

{sugar_business_card user_name=$userName user_id=$userId}


