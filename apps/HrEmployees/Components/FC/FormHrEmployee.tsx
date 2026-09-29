import Translator from '@hubleto/react-ui/core/Translator';
import Form from '@hubleto/react-ui/components/fc/Form';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormHrEmployeeProps extends FormProps {}

const componentName = 'FormHrEmployee';
const parentApp = 'Hubleto/App/Community/HrEmployees';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const FormHrEmployee = (props: FormHrEmployeeProps) => <Form
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/Employee'}
  urlSlug='hr-employees/employees'
  title={{fields: ['employee_number', 'job_title'], sub: T.translate('Employee profile')}}
  tabs={{default: {content: () => <div className='grid grid-cols-1 md:grid-cols-2 gap-2'>
    <Input field='id_user' />
    <Input field='employee_number' />
    <Input field='job_title' />
    <Input field='id_team' />
    <Input field='id_manager' />
    <Input field='id_employment_type' />
    <Input field='id_employment_status' />
    <Input field='id_workflow_step' />
    <Input field='date_hired' />
    <Input field='date_ended' />
    <Input field='id_work_location' />
    <Input field='notes' />
  </div>}}}
  {...props}
/>

export default FormHrEmployee;