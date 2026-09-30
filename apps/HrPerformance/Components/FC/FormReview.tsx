import Translator from '@hubleto/react-ui/core/Translator';
import Form from '@hubleto/react-ui/components/fc/Form';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

const componentName = 'FormReview';
const parentApp = 'Hubleto/App/Community/HrPerformance';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const FormReview = (props: FormProps) => <Form
  componentName={componentName}
  parentApp={parentApp}
  model={parentApp + '/Models/Review'}
  urlSlug='hr-performance/reviews'
  title={{field: 'period', sub: T.translate('Performance review')}}
  tabs={{default: {content: () => <div className='grid grid-cols-1 md:grid-cols-2 gap-2'>
    <Input field='id_user' />
    <Input field='id_reviewer' />
    <Input field='period' />
    <Input field='date_reviewed' />
    <Input field='score' />
    <Input field='status' />
    <Input field='id_workflow_step' />
    <Input field='summary' />
  </div>}}}
  {...props}
/>

export default FormReview;