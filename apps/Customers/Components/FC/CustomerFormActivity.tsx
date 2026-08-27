import React, { Component } from 'react';
import CalendarFormActivity from "@hubleto/apps/Calendar/Components/FC/CalendarFormActivity"

const CustomerFormActivity = (props: any) => {
  return <CalendarFormActivity
    id={props.id}
    calendarTab={props.calendarTab}
    customInputFields={['id_customer', 'id_contact']}
    defaultValues={{id_customer: props.idCustomer}}
    model='Hubleto/App/Community/Customers/Models/CustomerActivity'
    onClose={props.onClose}
  ></CalendarFormActivity>
}

export default CustomerFormActivity;