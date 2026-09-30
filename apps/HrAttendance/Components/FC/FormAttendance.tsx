import Translator from '@hubleto/react-ui/core/Translator';
import Form from '@hubleto/react-ui/components/fc/Form';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

const componentName = 'FormAttendance';
const parentApp = 'Hubleto/App/Community/HrAttendance';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const FormAttendance = (props: FormProps) => <Form
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/Attendance'}
  urlSlug='hr-attendance'
  title={{field: 'date_worked', sub: T.translate('Attendance record')}}
  tabs={{default: {content: () => <div className='grid grid-cols-1 md:grid-cols-2 gap-2'>
    <Input field='id_user' />
    <Input field='date_worked' />
    <Input field='time_in' />
    <Input field='time_out' />
    <Input field='break_minutes' />
    <Input field='id_workflow_step' />
    <Input field='is_approved' />
    <Input field='notes' />
  </div>}}}
  {...props}
/>

export default FormAttendance;