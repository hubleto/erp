import CalendarFormActivity from "@hubleto/apps/Calendar/Components/FC/CalendarFormActivity"

const MessageCalendarActivityForm = (props: any) => {
  return <CalendarFormActivity
    id={props.id}
    calendarTab={props.calendarTab}
    customInputFields={['id_message']}
    defaultValues={{id_message: props.idMessage, ...(props.defaultValues ?? {})}}
    model='Hubleto/App/Custom/LinkedinMessages/Models/MessageActivity'
    onClose={props.onClose}
    onAfterSaveRecord={props.onAfterSaveRecord}
  ></CalendarFormActivity>
}

export default MessageCalendarActivityForm;
