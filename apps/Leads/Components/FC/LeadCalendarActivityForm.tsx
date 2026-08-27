import CalendarFormActivity from "@hubleto/apps/Calendar/Components/FC/CalendarFormActivity"

const LeadCalendarActivityForm = (props: any) => {
  return <CalendarFormActivity
    id={props.id}
    calendarTab={props.calendarTab}
    customInputFields={['id_lead']}
    defaultValues={{id_lead: props.idLead}}
    model='Hubleto/App/Community/Leads/Models/LeadActivity'
    onClose={props.onClose}
  ></CalendarFormActivity>
}

export default LeadCalendarActivityForm;