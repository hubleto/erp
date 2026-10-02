import Translator from '@hubleto/react-ui/core/Translator';
import Form from '@hubleto/react-ui/components/fc/Form';
import { type FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

const componentName = 'FormLeave';
const parentApp = 'Hubleto/App/Community/HrLeave';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const FormLeave = (props: FormProps) => <Form
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/Leave'}
  urlSlug='hr-leave/balances'
  title={{field: 'year', sub: T.translate('Leave entitlement')}}
  tabs={{default: {content: () => <div className='grid grid-cols-1 md:grid-cols-2 gap-2'>
    <Input field='id_user' />
    <Input field='id_leave_type' />
    <Input field='year' />
    <Input field='days_entitled' />
    <Input field='days_carried_over' />
  </div>}}}
  {...props}
/>

export default FormLeave;