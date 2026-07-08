import numpy as try1
import pandas as try2

arr1 = try1.array([10,20,30,40,50])
print(arr1)

print(arr1[0])
print(arr1[4])
print(arr1[2])

print(arr1[1:3])
print(arr1[:3])

arr2=try1.array([1,2,3,4,5])
print(arr2.dtype)

arr2f=arr2.astype(float)
print(arr2f)
print(arr2f.dtype)


s1 = [100,200,300,400]

print(try2.Series(s1))
print(s1[1])

df1 = {
    'Name': ['Ana', 'Ben', 'Carl'],
    'Age': [20, 21, 19],   
    'Score': [85, 90, 88]  
}

df1 = try2.DataFrame(df1)
print(df1)
print('=='*10)

print(df1['Name'])

print(df1['Age'])


readcsv= try2.read_csv('stud.csv')
print(readcsv)

print(readcsv['Name'])
print(readcsv['Grade'])
print(readcsv.head(2))