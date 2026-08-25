import CalendarTabFormActivity from "@hubleto/apps/Calendar/Components/FC/CalendarTabFormActivity"

const LeadCalendarActivityForm = (props: any) => {
  return <CalendarTabFormActivity
    calendarTab={props.calendarTab}
    customInputFields={['id_lead']}
    defaultValues={{id_lead: props.id}}
    model='Hubleto/App/Community/Leads/Models/LeadActivity'
  ></CalendarTabFormActivity>
}

export default LeadCalendarActivityForm;