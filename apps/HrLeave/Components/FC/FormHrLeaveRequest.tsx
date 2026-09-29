import Translator from '@hubleto/react-ui/core/Translator';
import Form from '@hubleto/react-ui/components/fc/Form';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormHrLeaveRequestProps extends FormProps {}

const componentName = 'FormHrLeaveRequest';
const parentApp = 'Hubleto/App/Community/HrLeave';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const FormHrLeaveRequest = (props: FormHrLeaveRequestProps) => <Form
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/LeaveRequest'}
  urlSlug='hr-leave/requests'
  title={{fields: ['date_from', 'date_to'], sub: T.translate('Leave request')}}
  tabs={{default: {content: () => <div className='grid grid-cols-1 md:grid-cols-2 gap-2'>
    <Input field='id_user' />
    <Input field='id_leave_type' />
    <Input field='date_from' />
    <Input field='date_to' />
    <Input field='balance_year' />
    <Input field='days_requested' />
    <Input field='status' />
    <Input field='id_workflow_step' />
    <Input field='id_approver' />
    <Input field='date_decided' />
    <Input field='reason' />
  </div>}}}
  {...props}
/>

export default FormHrLeaveRequest;