import Translator from '@hubleto/react-ui/core/Translator';
import Form from '@hubleto/react-ui/components/fc/Form';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

const componentName = 'FormShift';
const parentApp = 'Hubleto/App/Community/HrAttendance';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const FormShift = (props: FormProps) => <Form
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/Shift'}
  urlSlug='hr-attendance/shifts'
  title={{field: 'date_start', sub: T.translate('Shift')}}
  tabs={{default: {content: () => <div className='grid grid-cols-1 md:grid-cols-2 gap-2'>
    <Input field='id_user' />
    <Input field='date_start' />
    <Input field='date_end' />
    <Input field='location' />
    <Input field='status' />
  </div>}}}
  {...props}
/>

export default FormShift;