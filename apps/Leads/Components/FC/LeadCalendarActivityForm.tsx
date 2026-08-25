import CalendarFormActivity from "@hubleto/apps/Calendar/Components/FC/CalendarFormActivity"

const LeadCalendarActivityForm = (props: any) => {
  return <CalendarFormActivity
    id={props.id}
    calendarTab={props.calendarTab}
    customInputFields={['id_lead']}
    defaultValues={{...props.defaultValues, id_lead: props.id}}
    model='Hubleto/App/Community/Leads/Models/LeadActivity'
  ></CalendarFormActivity>
}

export default LeadCalendarActivityForm;