import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormContact, { FormContactProps } from './FormContact';
import Spinner from '@hubleto/react-ui/components/fc/Spinner';

interface TableContactsProps extends TableProps {
  idCustomer: number,
  showAsCards?: boolean;
}

const componentName = 'TableContacts'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Contacts';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableContacts = (props: TableContactsProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Contact'}
    endpointParams={{idCustomer: props.idCustomer}}
    formUrlSlug='contacts'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_customer: props.idCustomer}}
    // getRowClassName={(table: TableMeta, rowData: any): string => { return table.getDefaultRowClassName(rowData); }}
    renderCell={(table: TableMeta, columnName: string, column: any, data: any, options: any) => {
      if (columnName == "virt_tags") {
        return data.TAGS.map((tag, key) => {
          return <div key={key} className="text-nowrap mr-2">
            <i style={{color: tag.TAG?.color}} className="fas fa-tag mr-2"></i>
            {tag.TAG?.name}
          </div>;
        });
      } else if (data.VALUES && data.VALUES.length > 0) {
        if (columnName == "virt_email") {
          let contactsRendered = 0;
          return (
            <div className='flex flex-row gap-2 flex-wrap max-w-lg'>
              {data.VALUES.map((value, key) => {
                if (value.type == "email" && contactsRendered < 2) {
                  contactsRendered += 1;
                  return (
                    <div key={data.id + '-email-' + key}>
                      {value.value} {value.CATEGORY ? <>({value.CATEGORY.name})</> : null}
                    </div>
                  );
                } else return null;
              })}
            </div>
          );
        } else if (columnName == "virt_number") {
          let contactsRendered = 0;
          return (
            <div className='flex flex-row gap-2 flex-wrap max-w-lg'>
              {data.VALUES.map((value, key) => {
                if (value.type == "number" && contactsRendered < 2) {
                  contactsRendered += 1;
                  return (
                    <div key={data.id + '-number-' + key}>
                      {value.value} {value.CATEGORY ? <>({value.CATEGORY.name})</> : null}
                    </div>
                  );
                } else return null;
              })}
            </div>
          );
        } else return table.renderDefaultCell(columnName, column, data, options);
      } else return table.renderDefaultCell(columnName, column, data, options);
    }}
    // renderActionsColumn={(table: TableMeta, row: any) => { return table.renderDefaultActionsColumn(row); }}
    // renderFooter={(table: TableMeta) => { return table.renderDefaultFooter(); }}
    renderContent={(table: TableMeta) => {
      if (props.showAsCards) {
        if (!table.data) {
          return <Spinner />;
        }

        return <>
          {/* <div className='flex gap-2'>
            {table.renderDefaultHeaderButtons()}
            <div className='[&_.table-header-search]:flex'>
              {table.renderDefaultFulltextSearch()}
            </div>
          </div> */}
          {table.renderDefaultFormModal()}
          <div className="md:grid md:grid-cols-2 gap-2 mt-1">
            {Object.keys(table.data?.records).map((key) => {
              const item = table.data.records[key];
              return <button
                key={key}
                className="btn btn-transparent w-full border-gray-300 shadow"
                onClick={() => { table.setRecordId(item.id); }}
              >
                <span className="icon">
                  <i className="fas fa-user text-2xl m-2"></i>
                </span>
                <span className="text flex-col" style={{maxHeight: "10em"}}>
                  <div className="flex gap-1">
                    {item.is_primary ? <div className="badge badge-small badge-violet">{T.translate("Primary")}</div> : null}
                    {item.is_for_invoicing ? <div className="badge badge-small badge-green">{T.translate("Invoicing")}</div> : null}
                  </div>
                  <div className="flex gap-2">
                    {item.salutation ?? ''}
                    <b>{item.first_name ?? ''}</b>
                    <b>{item.last_name ?? ''}</b>
                  </div>
                  <div className="flex gap-2">
                    {item.TAGS.map((tag, index) => {
                      return <div key={index} className="text-nowrap mr-2">
                        <i style={{color: tag.TAG?.color}} className="fas fa-tag mr-2"></i>
                        {tag.TAG?.name}
                      </div>;
                    })}
                  </div>
                  {item.VALUES.map((value, index) => {
                    return <div key={index} className='w-full truncate'><small>{value.value}</small></div>
                  })}
                </span>
              </button>;
            })}
          </div>
          <div className='mt-2'>
            {table.renderDefaultAddButton()}
          </div>
        </>;
      } else {
        return table.renderDefaultContent();
      }
    }}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormContact {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableContacts;
