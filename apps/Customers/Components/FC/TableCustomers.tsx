import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormCustomer, { FormCustomerProps } from './FormCustomer';

interface TableCustomersProps extends TableProps {}

const componentName = 'TableCustomers'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Customers';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableCustomers = (props: TableCustomersProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Customer'}
    formUrlSlug='customers'
    formModalProps={{type: 'right wide'}}
    renderCell={(table: TableMeta, columnName: string, column: any, data: any, options: any) => {
      if (columnName == "virt_tags") {
        return <div className='flex gap-1'>
          {data.TAGS.map((tag, key) => {
            return <div key={key} className="text-nowrap mr-2">
              <i style={{color: tag.TAG?.color}} className="fas fa-tag mr-2"></i>
              {tag.TAG?.name}
            </div>;
          })}
        </div>;
      } else {
        return table.renderDefaultCell(columnName, column, data, options);
      }
    }}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormCustomer {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableCustomers;
