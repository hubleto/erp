import Translator from '@hubleto/react-ui/core/Translator';
import Form from '@hubleto/react-ui/components/fc/Form';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

const componentName = 'FormGoal';
const parentApp = 'Hubleto/App/Community/HrPerformance';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const FormGoal = (props: FormProps) => <Form
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/Goal'}
  urlSlug='hr-performance/goals'
  title={{field: 'title', sub: T.translate('Employee goal')}}
  tabs={{default: {content: () => <div className='grid grid-cols-1 md:grid-cols-2 gap-2'>
    <Input field='id_user' />
    <Input field='title' />
    <Input field='description' />
    <Input field='date_due' />
    <Input field='progress' />
    <Input field='status' />
  </div>}}}
  {...props}
/>

export default FormGoal;