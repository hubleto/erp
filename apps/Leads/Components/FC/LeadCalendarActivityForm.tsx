import CalendarFormActivity from "@hubleto/apps/Calendar/Components/FC/CalendarFormActivity"

const LeadCalendarActivityForm = (props: any) => {
  console.log('LeadCalendarActivityForm', props);
  return <CalendarFormActivity
    id={props.id}
    calendarTab={props.calendarTab}
    customInputFields={['id_lead']}
    defaultValues={{id_lead: props.idLead}}
    model='Hubleto/App/Community/Leads/Models/LeadActivity'
  ></CalendarFormActivity>
}

export default LeadCalendarActivityForm;