import CalendarFormActivity from "@hubleto/apps/Calendar/Components/FC/CalendarFormActivity"

const OrderCalendarActivityForm = (props: any) => {
  return <CalendarFormActivity
    id={props.id}
    calendarTab={props.calendarTab}
    customInputFields={['id_order']}
    defaultValues={{id_order: props.idOrder}}
    model='Hubleto/App/Community/Orders/Models/OrderActivity'
    onClose={props.onClose}
  ></CalendarFormActivity>
}

export default OrderCalendarActivityForm;