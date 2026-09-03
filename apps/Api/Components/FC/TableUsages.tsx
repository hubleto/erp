import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormUsage, { FormUsageProps } from './FormUsage';

interface TableUsagesProps extends TableProps {
  idKey?: number,
}

const componentName = 'TableUsages'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Api';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableUsages = (props: TableUsagesProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Usage'}
    endpointParams={{idKey: props.idKey}}
    formUrlSlug='api/usages'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_key: props.idKey}}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormUsage {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableUsages;
