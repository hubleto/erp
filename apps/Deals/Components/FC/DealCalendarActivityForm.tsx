import CalendarFormActivity from "@hubleto/apps/Calendar/Components/FC/CalendarFormActivity"

const DealCalendarActivityForm = (props: any) => {
  return <CalendarFormActivity
    id={props.id}
    calendarTab={props.calendarTab}
    customInputFields={['id_deal']}
    defaultValues={{...props.defaultValues, id_deal: props.idDeal}}
    model='Hubleto/App/Community/Deals/Models/DealActivity'
  ></CalendarFormActivity>
}

export default DealCalendarActivityForm;